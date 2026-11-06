<?php

namespace Tests\Unit\Services\Amortization;

use App\Services\Amortization\Coverage;
use PHPUnit\Framework\TestCase;

class CoverageTest extends TestCase
{
    public function test_money_paid_covers_quotas_in_order_and_leaves_the_next_one_partial(): void
    {
        $coverage = Coverage::of($this->quotas(100_000, 100_000, 100_000), 250_000);

        $this->assertSame([100_000, 100_000, 50_000], $coverage->applied);
        $this->assertSame(2, $coverage->paidCount);
        $this->assertTrue($coverage->isPaid(1));
        $this->assertTrue($coverage->isPartial(2));
    }

    public function test_paying_more_than_one_quota_at_once_covers_several(): void
    {
        $coverage = Coverage::of($this->quotas(100_000, 100_000, 100_000), 300_000);

        $this->assertSame(3, $coverage->paidCount);
        $this->assertFalse($coverage->isPartial(3));
    }

    public function test_nothing_paid_leaves_every_quota_pending(): void
    {
        $coverage = Coverage::of($this->quotas(100_000, 100_000), 0);

        $this->assertSame(0, $coverage->paidCount);
        $this->assertFalse($coverage->isPaid(0));
        $this->assertFalse($coverage->isPartial(0));
    }

    /**
     * @return list<array<string, int>>
     */
    private function quotas(int ...$amounts): array
    {
        return array_map(fn (int $amount) => ['amount' => $amount], $amounts);
    }
}
