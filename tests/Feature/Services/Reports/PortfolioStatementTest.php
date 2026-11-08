<?php

namespace Tests\Feature\Services\Reports;

use App\Enums\PortfolioStatus;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Models\Promise;
use App\Services\Reports\PortfolioStatement;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class PortfolioStatementTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    /**
     * Six quotas of $100.000 due on the 15th from January 2026, seen on April 20th: one promise up to date,
     * one that never paid, one paid off, one with every quota covered but money still owed, cash promises
     * without a schedule (one signed in April), and a cancelled one.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-20');
        $this->signInToCampaign();

        $upToDate = $this->promiseWithQuotas('PROMESA 10');
        foreach (['2026-01-15' => 'R-ENERO', '2026-02-15' => 'R-FEBRERO', '2026-03-15' => 'R-MARZO', '2026-04-15' => 'R-ABRIL'] as $date => $receipt) {
            $this->pay($upToDate, 100_000, $date)->update(['bill_number' => $receipt]);
        }

        $this->promiseWithQuotas('PROMESA 2');

        $this->pay($this->promiseWithQuotas('PROMESA 3'), 600_000, '2026-02-01');

        $owesMore = $this->promiseWithQuotas('PROMESA 4');
        $owesMore->update(['value' => 700_000]);
        $this->pay($owesMore, 600_000, '2026-02-01');

        Promise::create(['number' => 'PROMESA 5', 'signature_date' => '2026-01-01', 'value' => 1_000_000, 'payment_method' => PromisePaymentMethod::CASH]);
        $this->promiseWithQuotas('PROMESA 6')->update(['status' => PromiseStatus::CANCELLED]);
        Promise::create(['number' => 'PROMESA 7', 'signature_date' => '2026-04-10', 'value' => 500_000, 'payment_method' => PromisePaymentMethod::CASH]);
    }

    public function test_lists_every_active_promise_in_natural_order_including_those_without_payments(): void
    {
        $statement = new PortfolioStatement(CarbonImmutable::today());

        $this->assertSame([
            'PROMESA 2' => PortfolioStatus::OVER_90,
            'PROMESA 3' => PortfolioStatus::PAID,
            'PROMESA 4' => PortfolioStatus::CHECK_BALANCE,
            'PROMESA 5' => PortfolioStatus::NO_SCHEDULE,
            'PROMESA 7' => PortfolioStatus::NO_SCHEDULE,
            'PROMESA 10' => PortfolioStatus::UP_TO_DATE,
        ], $this->statuses($statement));

        $neverPaid = $statement->entries->first();
        $this->assertTrue($neverPaid['lastPayments']->isEmpty());
        $this->assertSame([400_000, 95, 600_000], [$neverPaid['standing']->overdueAmount, $neverPaid['standing']->daysOverdue, $neverPaid['balance']]);
        $this->assertSame(['R-ABRIL'], $statement->entries->last()['lastPayments']->pluck('bill_number')->all());
    }

    public function test_on_a_past_cut_off_date_counts_only_the_payments_made_by_then(): void
    {
        $statement = new PortfolioStatement(CarbonImmutable::parse('2026-03-31'));
        $entries = $statement->entries->keyBy(fn (array $entry) => $entry['promise']->number);

        $this->assertArrayNotHasKey('PROMESA 7', $entries->all());
        $this->assertSame(PortfolioStatus::UP_TO_90, $entries['PROMESA 2']['status']);
        $this->assertSame([300_000, 75], [$entries['PROMESA 2']['standing']->overdueAmount, $entries['PROMESA 2']['standing']->daysOverdue]);

        $upToDate = $entries['PROMESA 10'];
        $this->assertSame(PortfolioStatus::UP_TO_DATE, $upToDate['status']);
        $this->assertSame(['R-MARZO'], $upToDate['lastPayments']->pluck('bill_number')->all());
        $this->assertSame([300_000, 300_000], [$upToDate['paid'], $upToDate['balance']]);
        $this->assertSame('2026-04-15', $upToDate['standing']->currentQuota['due_date']);
    }

    public function test_can_keep_only_the_promises_in_arrears(): void
    {
        $this->assertSame(['PROMESA 2' => PortfolioStatus::OVER_90], $this->statuses(new PortfolioStatement(CarbonImmutable::today(), onlyLate: true)));
    }

    public function test_sums_promises_balance_and_arrears_per_status(): void
    {
        $summary = collect((new PortfolioStatement(CarbonImmutable::today()))->summary())
            ->mapWithKeys(fn (array $group) => [$group['status']->value => [$group['count'], $group['balance'], $group['overdue']]]);

        $this->assertSame([
            'up_to_date' => [1, 200_000, 0],
            'up_to_30' => [0, 0, 0],
            'up_to_90' => [0, 0, 0],
            'over_90' => [1, 600_000, 400_000],
            'paid' => [1, 0, 0],
            'check_balance' => [1, 100_000, 0],
            'no_schedule' => [2, 1_500_000, 0],
        ], $summary->all());
    }

    /**
     * @return array<string, PortfolioStatus>
     */
    private function statuses(PortfolioStatement $statement): array
    {
        return $statement->entries->mapWithKeys(fn (array $entry) => [$entry['promise']->number => $entry['status']])->all();
    }
}
