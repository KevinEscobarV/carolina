<?php

namespace App\Services\Amortization;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Where a promise stands by the money paid, not by the number of payments: the quota it is on, what is
 * left of it, and how much is overdue. A quota is overdue once its due date plus the grace days has
 * passed and the payments do not cover it. Differences under the tolerance (rounding) are not arrears.
 */
final readonly class Standing
{
    public const TOLERANCE = 1_000;

    /**
     * @param  array<string, mixed>|null  $currentQuota  first quota not fully paid; null when all are paid
     */
    private function __construct(
        public Coverage $coverage,
        public ?int $currentIndex,
        public ?array $currentQuota,
        public int $currentPending,
        public int $overdueAmount,
        public int $daysOverdue,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function of(array $rows, float $paid, CarbonInterface $today, int $graceDays = 0): self
    {
        $rows = array_values($rows);
        $coverage = Coverage::of($rows, $paid + self::TOLERANCE);
        $index = $coverage->paidCount < count($rows) ? $coverage->paidCount : null;
        $current = $index === null ? null : $rows[$index];

        // Quotas due before this date are late
        $lateBefore = CarbonImmutable::parse($today->toDateString())->subDays(max(0, $graceDays))->toDateString();
        $lateAmount = array_sum(array_map(fn (array $row) => $row['due_date'] < $lateBefore ? (int) $row['amount'] : 0, $rows));
        $overdue = max(0, $lateAmount - (int) round($paid));

        $isOverdue = $current !== null && $current['due_date'] < $lateBefore && $overdue >= self::TOLERANCE;

        return new self(
            coverage: $coverage,
            currentIndex: $index,
            currentQuota: $current,
            currentPending: $current === null ? 0 : max(0, (int) $current['amount'] - (int) round($paid) + array_sum(array_slice(array_column($rows, 'amount'), 0, $index))),
            overdueAmount: $isOverdue ? $overdue : 0,
            daysOverdue: $isOverdue ? (int) CarbonImmutable::parse($current['due_date'])->diffInDays(CarbonImmutable::parse($today->toDateString())) : 0,
        );
    }

    public function isOverdue(): bool
    {
        return $this->overdueAmount > 0;
    }

    /**
     * Quotas fully covered by the payments.
     */
    public function paidQuotas(): int
    {
        return $this->coverage->paidCount;
    }
}
