<?php

namespace Tests\Unit\Services\Amortization;

use App\Enums\InterestRateType;
use App\Enums\PaymentFrequency;
use App\Enums\RebalanceMode;
use App\Services\Amortization\InterestRate;
use App\Services\Amortization\ScheduleCalculator;
use App\Services\Amortization\ScheduleException;
use App\Services\Amortization\ScheduleTerms;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ScheduleCalculatorTest extends TestCase
{
    private ScheduleCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new ScheduleCalculator;
    }

    public function test_quotas_share_the_principal_and_the_last_one_absorbs_the_rounding(): void
    {
        $rows = $this->calculator->byNumberOfQuotas($this->terms(1_000_000), 3);

        $this->assertSame([333_333, 333_333, 333_334], array_column($rows, 'amount'));
        $this->assertSame([666_667, 333_334, 0], array_column($rows, 'balance'));
        $this->assertSame([1, 2, 3], array_column($rows, 'number'));
    }

    public function test_monthly_due_dates_keep_the_day_of_the_month_without_overflowing(): void
    {
        $rows = $this->calculator->byNumberOfQuotas($this->terms(400_000, firstDueDate: '2026-01-31'), 4);

        $this->assertSame(['2026-01-31', '2026-02-28', '2026-03-31', '2026-04-30'], array_column($rows, 'due_date'));
    }

    public function test_interest_follows_the_fixed_quota_system(): void
    {
        $rows = $this->calculator->byNumberOfQuotas($this->terms(10_000_000, rate: new InterestRate(1)), 12);

        $this->assertSame(888_488, $rows[0]['amount']);
        $this->assertSame(100_000, $rows[0]['interest']);
        $this->assertSame(788_488, $rows[0]['principal']);
        $this->assertSame(0, end($rows)['balance']);
        $this->assertSame(10_000_000, array_sum(array_column($rows, 'principal')));
    }

    public function test_effective_annual_rate_charges_its_quarterly_equivalent(): void
    {
        $rate = new InterestRate((1.03 ** 4 - 1) * 100, InterestRateType::EFFECTIVE_ANNUAL);

        $rows = $this->calculator->byNumberOfQuotas($this->terms(1_000_000, frequency: PaymentFrequency::QUARTERLY, rate: $rate), 4);

        $this->assertSame(30_000, $rows[0]['interest']);
        $this->assertSame(['2026-01-15', '2026-04-15', '2026-07-15', '2026-10-15'], array_column($rows, 'due_date'));
    }

    public function test_extraordinary_quotas_take_their_amount_and_ordinary_ones_share_the_rest(): void
    {
        $terms = $this->terms(12_000_000, extraordinaryMonths: [6, 12], extraordinaryAmount: 2_000_000);

        $rows = $this->calculator->byNumberOfQuotas($terms, 12);

        $this->assertSame(
            [800_000, 800_000, 800_000, 800_000, 800_000, 2_000_000, 800_000, 800_000, 800_000, 800_000, 800_000, 2_000_000],
            array_column($rows, 'amount'),
        );
        $this->assertSame([5, 11], array_keys(array_filter(array_column($rows, 'is_extraordinary'))));
    }

    public function test_extraordinary_quotas_cannot_cover_the_whole_balance(): void
    {
        $this->expectException(ScheduleException::class);

        $this->calculator->byNumberOfQuotas($this->terms(3_000_000, extraordinaryMonths: [6, 12], extraordinaryAmount: 2_000_000), 12);
    }

    public function test_fixed_quota_adds_quotas_until_the_balance_is_paid(): void
    {
        $rows = $this->calculator->byQuotaAmount($this->terms(1_000_000), 300_000);

        $this->assertSame([300_000, 300_000, 300_000, 100_000], array_column($rows, 'amount'));
        $this->assertSame('2026-04-15', end($rows)['due_date']);
    }

    public function test_fixed_quota_must_pay_more_than_the_interest_of_the_period(): void
    {
        $this->expectException(ScheduleException::class);

        $this->calculator->byQuotaAmount($this->terms(1_000_000, rate: new InterestRate(1)), 10_000);
    }

    public function test_irregular_schedule_charges_interest_for_the_days_between_quotas(): void
    {
        $terms = $this->terms(1_000_000, frequency: PaymentFrequency::IRREGULAR, rate: new InterestRate(12, InterestRateType::EFFECTIVE_ANNUAL), startDate: '2026-01-01');
        $rows = [$this->row('2027-01-01', 600_000), $this->row('2028-01-01', 1)];

        $rows = $this->calculator->recalculate($rows, $terms);

        $this->assertSame([120_000, 62_400], array_column($rows, 'interest'));
        $this->assertSame([600_000, 582_400], array_column($rows, 'amount'));
        $this->assertSame(0, $rows[1]['balance']);
    }

    public function test_raising_a_quota_lowers_the_following_ones_when_the_promise_reduces_the_amount(): void
    {
        $rows = $this->sixQuotasOf100k();
        $rows[1]['amount'] = 200_000;

        $rows = $this->calculator->rebalance($rows, 1, $this->terms(600_000), RebalanceMode::REDUCE_AMOUNT);

        $this->assertSame([100_000, 200_000, 75_000, 75_000, 75_000, 75_000], array_column($rows, 'amount'));
        $this->assertSame(0, end($rows)['balance']);
    }

    public function test_raising_a_quota_removes_quotas_at_the_end_when_the_promise_reduces_the_count(): void
    {
        $rows = $this->sixQuotasOf100k();
        $rows[1]['amount'] = 200_000;

        $rows = $this->calculator->rebalance($rows, 1, $this->terms(600_000), RebalanceMode::REDUCE_COUNT);

        $this->assertSame([100_000, 200_000, 100_000, 100_000, 100_000], array_column($rows, 'amount'));
    }

    public function test_lowering_a_quota_adds_quotas_at_the_end_when_the_promise_reduces_the_count(): void
    {
        $rows = $this->sixQuotasOf100k();
        $rows[1]['amount'] = 50_000;

        $rows = $this->calculator->rebalance($rows, 1, $this->terms(600_000), RebalanceMode::REDUCE_COUNT);

        $this->assertSame([100_000, 50_000, 100_000, 100_000, 100_000, 100_000, 50_000], array_column($rows, 'amount'));
        $this->assertSame('2026-07-15', end($rows)['due_date']);
        $this->assertSame(7, end($rows)['number']);
    }

    public function test_rebalancing_the_amount_leaves_extraordinary_quotas_as_they_are(): void
    {
        $terms = $this->terms(12_000_000, extraordinaryMonths: [6, 12], extraordinaryAmount: 2_000_000);
        $rows = $this->calculator->byNumberOfQuotas($terms, 12);
        $rows[0]['amount'] = 1_600_000;

        $rows = $this->calculator->rebalance($rows, 0, $terms, RebalanceMode::REDUCE_AMOUNT);

        $this->assertSame(2_000_000, $rows[5]['amount']);
        // (12,000,000 - 1,600,000 - 2 × 2,000,000) shared by the 9 ordinary quotas left
        $this->assertSame(711_111, $rows[1]['amount']);
        $this->assertSame(0, end($rows)['balance']);
    }

    public function test_a_quota_that_covers_the_balance_closes_the_schedule(): void
    {
        $rows = $this->sixQuotasOf100k();
        $rows[1]['amount'] = 900_000;

        $rows = $this->calculator->rebalance($rows, 1, $this->terms(600_000), RebalanceMode::REDUCE_AMOUNT);

        $this->assertSame([100_000, 500_000], array_column($rows, 'amount'));
    }

    public function test_moving_a_due_date_moves_the_following_ones_with_the_frequency(): void
    {
        $rows = $this->calculator->moveDueDate($this->sixQuotasOf100k(), 1, CarbonImmutable::parse('2026-03-01'), $this->terms(600_000));

        $this->assertSame(['2026-01-15', '2026-03-01', '2026-04-01', '2026-05-01', '2026-06-01', '2026-07-01'], array_column($rows, 'due_date'));
    }

    public function test_moving_a_due_date_in_an_irregular_schedule_moves_only_that_quota(): void
    {
        $terms = $this->terms(600_000, frequency: PaymentFrequency::IRREGULAR);

        $rows = $this->calculator->moveDueDate($this->sixQuotasOf100k(), 1, CarbonImmutable::parse('2026-03-01'), $terms);

        $this->assertSame(['2026-01-15', '2026-03-01', '2026-03-15'], array_slice(array_column($rows, 'due_date'), 0, 3));
    }

    public function test_a_due_date_cannot_be_earlier_than_the_previous_quota(): void
    {
        $this->expectException(ScheduleException::class);

        $this->calculator->moveDueDate($this->sixQuotasOf100k(), 2, CarbonImmutable::parse('2026-01-01'), $this->terms(600_000, frequency: PaymentFrequency::IRREGULAR));
    }

    /**
     * @param  list<int>  $extraordinaryMonths
     */
    private function terms(
        int $principal,
        string $firstDueDate = '2026-01-15',
        PaymentFrequency $frequency = PaymentFrequency::MONTHLY,
        InterestRate $rate = new InterestRate,
        ?string $startDate = null,
        array $extraordinaryMonths = [],
        int $extraordinaryAmount = 0,
    ): ScheduleTerms {
        return new ScheduleTerms(
            principal: $principal,
            firstDueDate: CarbonImmutable::parse($firstDueDate),
            frequency: $frequency,
            rate: $rate,
            startDate: $startDate ? CarbonImmutable::parse($startDate) : null,
            extraordinaryMonths: $extraordinaryMonths,
            extraordinaryAmount: $extraordinaryAmount,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function sixQuotasOf100k(): array
    {
        return $this->calculator->byNumberOfQuotas($this->terms(600_000), 6);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(string $dueDate, int $amount): array
    {
        return ['number' => 0, 'due_date' => $dueDate, 'amount' => $amount, 'interest' => 0, 'principal' => 0, 'balance' => 0, 'is_extraordinary' => false];
    }
}
