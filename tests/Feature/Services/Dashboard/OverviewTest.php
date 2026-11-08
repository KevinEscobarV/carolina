<?php

namespace Tests\Feature\Services\Dashboard;

use App\Enums\PaymentMethod;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Livewire\Dashboard\Index\Page;
use App\Models\Promise;
use App\Services\Dashboard\Overview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesPromises;
use Tests\TestCase;

class OverviewTest extends TestCase
{
    use CreatesPromises, RefreshDatabase;

    /**
     * Four promises of six quotas of $100.000 due on the 15th from January 2026, seen on April 20th:
     * one up to date, one late since April, one since February and one that never paid. A cancelled
     * promise with a payment this month must not count anywhere.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-20');
        $this->signInToCampaign();

        $upToDate = $this->promiseWithQuotas('P-AL-DIA');
        foreach (['2026-01-15', '2026-02-15', '2026-03-15', '2026-04-15'] as $date) {
            $this->pay($upToDate, 100_000, $date);
        }

        $lateSinceApril = $this->promiseWithQuotas('P-ABRIL');
        foreach (['2026-01-15', '2026-02-15', '2026-03-15'] as $date) {
            $this->pay($lateSinceApril, 100_000, $date);
        }

        $lateSinceFebruary = $this->promiseWithQuotas('P-FEBRERO');
        $this->pay($lateSinceFebruary, 100_000, '2026-01-15')->update(['payment_method' => PaymentMethod::BANK_TRANSFER]);

        $this->promiseWithQuotas('P-NUNCA');

        $cancelled = $this->promiseWithQuotas('P-CANCELADA');
        $cancelled->update(['status' => PromiseStatus::CANCELLED]);
        $this->pay($cancelled, 600_000, '2026-04-01');
    }

    public function test_totals_leave_out_cancelled_promises_and_count_arrears_by_amount(): void
    {
        $totals = (new Overview)->totals();

        $this->assertSame(2_400_000, $totals['sales']);
        $this->assertSame(4, $totals['promises']);
        $this->assertSame(800_000, $totals['collected']);
        $this->assertSame(1_600_000, $totals['receivable']);
        $this->assertSame(800_000, $totals['overdue']);
        $this->assertSame(3, $totals['overduePromises']);
        $this->assertSame(100_000, $totals['thisMonth']);
        $this->assertSame(200_000, $totals['lastMonth']);
    }

    public function test_groups_the_promises_by_days_overdue(): void
    {
        $this->assertSame([
            ['key' => 'up_to_date', 'label' => 'Al día', 'count' => 1, 'amount' => 0],
            ['key' => 'up_to_30', 'label' => '1 a 30 días', 'count' => 1, 'amount' => 100_000],
            ['key' => 'up_to_90', 'label' => '31 a 90 días', 'count' => 1, 'amount' => 300_000],
            ['key' => 'over_90', 'label' => 'Más de 90 días', 'count' => 1, 'amount' => 400_000],
        ], (new Overview)->aging());
    }

    public function test_compares_collected_and_scheduled_money_per_month_with_nothing_collected_ahead(): void
    {
        $monthly = (new Overview)->monthly();
        $january = Overview::MONTHS_BACK - 3;
        $april = Overview::MONTHS_BACK;

        $this->assertCount(Overview::MONTHS_BACK + Overview::MONTHS_AHEAD + 1, $monthly['labels']);
        $this->assertSame('enero de 2026', $monthly['months'][$january]);
        $this->assertSame([300_000, 400_000], [$monthly['collected'][$january], $monthly['scheduled'][$january]]);
        $this->assertSame([100_000, 400_000], [$monthly['collected'][$april], $monthly['scheduled'][$april]]);

        // May is the first month ahead: scheduled but not collected yet; after June nothing is due
        $this->assertSame($april + 1, $monthly['firstFuture']);
        $this->assertSame([null, 400_000], [$monthly['collected'][$april + 1], $monthly['scheduled'][$april + 1]]);
        $this->assertSame([null, 0], [$monthly['collected'][$april + 3], $monthly['scheduled'][$april + 3]]);
    }

    public function test_ranks_payment_methods_by_money_collected(): void
    {
        $this->assertSame([
            ['label' => PaymentMethod::CASH->label(), 'amount' => 700_000, 'share' => 87.5],
            ['label' => PaymentMethod::BANK_TRANSFER->label(), 'amount' => 100_000, 'share' => 12.5],
        ], (new Overview)->paymentMethods());
    }

    public function test_counts_the_promises_signed_per_month_against_the_twelve_months_before(): void
    {
        Promise::create(['number' => 'P-2025', 'signature_date' => '2025-03-10', 'value' => 500_000, 'payment_method' => PromisePaymentMethod::CASH]);

        $sales = (new Overview)->sales();

        $this->assertSame([4, 2_400_000, 1, 500_000], [$sales['count'], $sales['value'], $sales['previousCount'], $sales['previousValue']]);
        $this->assertCount(12, $sales['months']);
        $this->assertSame('mayo de 2025', $sales['months'][0]['label']);
        $this->assertSame(['label' => 'enero de 2026', 'count' => 4, 'value' => 2_400_000], $sales['months'][8]);
        $this->assertSame(0, $sales['months'][11]['count']);
    }

    public function test_lists_only_the_quotas_of_promises_up_to_date_due_in_the_next_days(): void
    {
        $upcoming = (new Overview)->upcoming();

        $this->assertSame(1, $upcoming['count']);
        $this->assertSame(100_000, $upcoming['total']);
        $this->assertSame('P-AL-DIA', $upcoming['items'][0]['promise']->number);
        $this->assertSame('2026-05-15', $upcoming['items'][0]['due_date']->toDateString());
    }

    public function test_the_dashboard_shows_the_figures(): void
    {
        Livewire::test(Page::class)
            ->assertSee('$2.400.000')
            ->assertSee('3 promesas atrasadas')
            ->assertSee('P-AL-DIA')
            ->assertDontSee('P-CANCELADA');
    }
}
