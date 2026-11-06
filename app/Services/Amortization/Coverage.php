<?php

namespace App\Services\Amortization;

/**
 * How the money paid covers the quotas, in order: each quota takes what it can from what is left.
 * The leading quotas fully covered are paid; the next one is partial when it got part of the money.
 */
final readonly class Coverage
{
    /**
     * @param  list<int>  $applied  money applied to each quota
     * @param  list<int>  $amounts  amount of each quota
     */
    private function __construct(
        public array $applied,
        public array $amounts,
        public int $paidCount,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function of(array $rows, float $paid): self
    {
        $left = (int) round($paid);
        $applied = [];
        $amounts = [];

        foreach (array_values($rows) as $row) {
            $amount = (int) $row['amount'];
            $take = max(0, min($left, $amount));
            $applied[] = $take;
            $amounts[] = $amount;
            $left -= $take;
        }

        $paidCount = 0;
        while ($paidCount < count($amounts) && $applied[$paidCount] >= $amounts[$paidCount]) {
            $paidCount++;
        }

        return new self($applied, $amounts, $paidCount);
    }

    public function isPaid(int $index): bool
    {
        return $index < $this->paidCount;
    }

    public function isPartial(int $index): bool
    {
        return $index === $this->paidCount && ($this->applied[$index] ?? 0) > 0;
    }
}
