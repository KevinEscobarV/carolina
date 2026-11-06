<?php

namespace Tests\Unit\Services\Amortization;

use App\Services\Amortization\Standing;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class StandingTest extends TestCase
{
    public function test_unpaid_quota_past_its_due_date_is_overdue_by_the_missing_amount(): void
    {
        $standing = Standing::of($this->monthlyQuotas(), 100_000, $this->today('2026-03-20'));

        // February and March are both due and unpaid
        $this->assertTrue($standing->isOverdue());
        $this->assertSame(200_000, $standing->overdueAmount);
        $this->assertSame(33, $standing->daysOverdue);
        $this->assertSame('2026-02-15', $standing->currentQuota['due_date']);
    }

    public function test_paying_several_quotas_at_once_is_not_arrears(): void
    {
        $standing = Standing::of($this->monthlyQuotas(), 300_000, $this->today('2026-03-20'));

        $this->assertFalse($standing->isOverdue());
        $this->assertSame(3, $standing->paidQuotas());
        $this->assertSame('2026-04-15', $standing->currentQuota['due_date']);
    }

    public function test_grace_days_delay_the_arrears(): void
    {
        $quotas = $this->monthlyQuotas();

        $this->assertFalse(Standing::of($quotas, 0, $this->today('2026-01-18'), graceDays: 5)->isOverdue());
        $this->assertTrue(Standing::of($quotas, 0, $this->today('2026-01-21'), graceDays: 5)->isOverdue());
    }

    public function test_a_shortfall_below_the_tolerance_is_not_arrears(): void
    {
        $standing = Standing::of($this->monthlyQuotas(), 199_500, $this->today('2026-02-20'));

        $this->assertFalse($standing->isOverdue());
    }

    public function test_the_current_quota_shows_what_is_left_of_it_after_a_partial_payment(): void
    {
        $standing = Standing::of($this->monthlyQuotas(), 150_000, $this->today('2026-01-20'));

        $this->assertSame(1, $standing->currentIndex);
        $this->assertSame(50_000, $standing->currentPending);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function monthlyQuotas(): array
    {
        return array_map(fn (string $date) => ['due_date' => $date, 'amount' => 100_000], [
            '2026-01-15', '2026-02-15', '2026-03-15', '2026-04-15', '2026-05-15', '2026-06-15',
        ]);
    }

    private function today(string $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date);
    }
}
