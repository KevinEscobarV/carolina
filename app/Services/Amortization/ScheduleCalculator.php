<?php

namespace App\Services\Amortization;

use App\Enums\PaymentFrequency;
use App\Enums\RebalanceMode;
use Carbon\CarbonImmutable;

/**
 * Builds and rebalances amortization schedules with a fixed quota (French system), in whole pesos.
 * Each period charges interest on the balance and the rest of the quota pays the principal. The last
 * quota always closes the balance at zero, so it absorbs the rounding.
 *
 * A schedule is a list of rows:
 * array{number: int, due_date: string, amount: int, interest: int, principal: int, balance: int, is_extraordinary: bool, is_prepayment: bool}
 */
class ScheduleCalculator
{
    public const MAX_QUOTAS = 600;

    /**
     * Schedule with a set number of quotas: the extraordinary ones take their amount and the
     * ordinary ones share the rest equally.
     *
     * @return list<array<string, mixed>>
     */
    public function byNumberOfQuotas(ScheduleTerms $terms, int $count): array
    {
        $this->ensurePrincipal($terms);

        if ($count < 1 || $count > self::MAX_QUOTAS) {
            throw new ScheduleException('El número de cuotas debe estar entre 1 y '.self::MAX_QUOTAS.'.');
        }

        $rows = [];
        for ($index = 0; $index < $count; $index++) {
            $rows[] = $this->newRow($terms, $terms->dueDateAfter($terms->firstDueDate, $index));
        }

        return $this->amortize($this->spread($rows, 0, $terms, strict: true), 0, $terms, closeLast: true);
    }

    /**
     * Schedule with a set quota value: quotas are added until the balance is paid; the last one is smaller.
     *
     * @return list<array<string, mixed>>
     */
    public function byQuotaAmount(ScheduleTerms $terms, int $quota): array
    {
        $this->ensurePrincipal($terms);

        if ($quota <= 0) {
            throw new ScheduleException('El valor de la cuota debe ser mayor que cero.');
        }

        return $this->amortize([], 0, $terms, closeLast: false, appendQuota: $quota);
    }

    /**
     * After the amount of quota $index changed: that quota keeps its new amount (up to what is owed)
     * and the following ones adapt as the promise chose.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function rebalance(array $rows, int $index, ScheduleTerms $terms, RebalanceMode $mode): array
    {
        $rows = array_values($rows);
        $head = $this->amortize(array_slice($rows, 0, $index + 1), $index, $terms, closeLast: false);

        // The edited quota paid off everything: the following ones are no longer needed
        if ($head === [] || end($head)['balance'] <= 0) {
            return $head;
        }

        $from = $index + 1;
        $rows = [...$head, ...array_slice($rows, $from)];

        if ($mode === RebalanceMode::REDUCE_COUNT) {
            return $this->amortize($rows, $from, $terms, closeLast: false, appendQuota: $this->followingQuota($rows, $index));
        }

        if ($from === count($rows)) {
            $rows[] = $this->newRow($terms, $this->nextDueDate($rows, $terms));
        }

        return $this->amortize($this->spread($rows, $from, $terms), $from, $terms, closeLast: true);
    }

    /**
     * Turns the money paid beyond what is due by $today into an extra payment: a quota dated $today for
     * that amount goes after the quotas already due, and the following ones adapt as the promise chose
     * (fewer quotas at the end, or lower quotas).
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function applyPrepayment(array $rows, float $paid, CarbonImmutable $today, ScheduleTerms $terms, RebalanceMode $mode): array
    {
        $rows = array_values($rows);
        $dueCount = 0;
        while ($dueCount < count($rows) && $rows[$dueCount]['due_date'] <= $today->toDateString()) {
            $dueCount++;
        }

        $prepayment = (int) round($paid) - array_sum(array_column(array_slice($rows, 0, $dueCount), 'amount'));

        if ($prepayment <= 0) {
            throw new ScheduleException('No hay abonos por aplicar: lo pagado no supera lo que ya venció.');
        }

        if ($dueCount === count($rows)) {
            throw new ScheduleException('Todas las cuotas ya vencieron: no quedan cuotas siguientes que ajustar.');
        }

        array_splice($rows, $dueCount, 0, [[
            ...$this->newRow($terms, $today, $prepayment),
            'amount' => $prepayment,
            'is_extraordinary' => false,
            'is_prepayment' => true,
        ]]);

        return $this->rebalance($rows, $dueCount, $terms, $mode);
    }

    /**
     * Adds a quota after the last one and spreads the pending balance over the ordinary quotas from $from on.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function addQuota(array $rows, int $from, ScheduleTerms $terms): array
    {
        $rows = array_values($rows);
        $rows[] = $this->newRow($terms, $this->nextDueDate($rows, $terms));

        return $this->redistribute($rows, $from, $terms);
    }

    /**
     * Removes quota $index and spreads the pending balance over the ordinary quotas from $from on.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function removeQuota(array $rows, int $index, int $from, ScheduleTerms $terms): array
    {
        $rows = array_values($rows);
        array_splice($rows, $index, 1);

        if ($rows === []) {
            throw new ScheduleException('La proyección debe tener al menos una cuota.');
        }

        return $this->redistribute($rows, min($from, count($rows) - 1), $terms);
    }

    /**
     * Spreads the balance pending at quota $from over the ordinary quotas from there on.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function redistribute(array $rows, int $from, ScheduleTerms $terms): array
    {
        return $this->amortize($this->spread(array_values($rows), $from, $terms), $from, $terms, closeLast: true);
    }

    /**
     * Recalculates interest, principal and balance keeping the amounts; the last quota closes the balance.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function recalculate(array $rows, ScheduleTerms $terms, int $from = 0): array
    {
        return $this->amortize($rows, $from, $terms, closeLast: true);
    }

    /**
     * Moves the due date of quota $index. With a regular frequency the following quotas keep the same
     * spacing from the new date; in an irregular schedule only that quota moves.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function moveDueDate(array $rows, int $index, CarbonImmutable $date, ScheduleTerms $terms): array
    {
        $rows = array_values($rows);
        $rows[$index]['due_date'] = $date->toDateString();

        if ($terms->frequency !== PaymentFrequency::IRREGULAR) {
            for ($next = $index + 1; $next < count($rows); $next++) {
                $rows[$next]['due_date'] = $terms->dueDateAfter($date, $next - $index)->toDateString();
            }
        }

        $this->ensureAscendingDates($rows);

        return $this->recalculate($rows, $terms, $index);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public function ensureAscendingDates(array $rows): void
    {
        foreach (array_values($rows) as $index => $row) {
            if ($index > 0 && $row['due_date'] < $rows[$index - 1]['due_date']) {
                throw new ScheduleException('La cuota '.($index + 1).' vence antes que la cuota anterior.');
            }
        }
    }

    /**
     * Walks the quotas from $from on with their amounts: interest on the balance, the rest to principal.
     * A quota that covers what is owed closes the schedule and later ones are dropped. With $closeLast the
     * last quota pays whatever is left; with $appendQuota, quotas of that value are added until the
     * balance is paid.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function amortize(array $rows, int $from, ScheduleTerms $terms, bool $closeLast, ?int $appendQuota = null): array
    {
        $rows = array_values($rows);
        $result = array_slice($rows, 0, $from);
        $pending = array_slice($rows, $from);
        $balance = $this->openingBalance($rows, $from, $terms);
        $previous = $this->previousDueDate($rows, $from);

        for ($index = 0; $balance > 0; $index++) {
            $isAppended = $index >= count($pending);

            if ($isAppended && $appendQuota === null) {
                break;
            }

            if (count($result) >= self::MAX_QUOTAS) {
                throw new ScheduleException('El plan supera las '.self::MAX_QUOTAS.' cuotas: aumenta el valor de la cuota.');
            }

            $row = $isAppended ? $this->newRow($terms, $this->nextDueDate($result, $terms), $appendQuota) : $pending[$index];
            $date = CarbonImmutable::parse($row['due_date']);
            // An extra payment lowers the balance between two quotas and charges no interest of its own
            $interest = empty($row['is_prepayment']) ? (int) round($balance * $terms->rateFor($date, $previous)) : 0;
            $owed = $balance + $interest;
            $amount = (int) $row['amount'];

            if ($isAppended && ! $row['is_extraordinary'] && $amount <= $interest) {
                throw new ScheduleException('La cuota no alcanza a pagar los intereses de cada período: aumenta su valor.');
            }

            $isLast = $closeLast && $index === count($pending) - 1;
            if ($amount >= $owed || $isLast) {
                $amount = $owed;
            }

            $balance = $owed - $amount;
            $result[] = [
                ...$row,
                'number' => count($result) + 1,
                'amount' => $amount,
                'interest' => $interest,
                'principal' => $amount - $interest,
                'balance' => $balance,
            ];
            $previous = $date;
        }

        return $result;
    }

    /**
     * Sets the ordinary quotas from $from on to the value that pays off the balance, leaving the
     * extraordinary ones as they are (present value of both, at the rate of each period).
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function spread(array $rows, int $from, ScheduleTerms $terms, bool $strict = false): array
    {
        $discount = 1.0;
        $extraordinaryValue = 0.0;
        $ordinaryFactor = 0.0;
        $previous = $this->previousDueDate($rows, $from);

        for ($index = $from; $index < count($rows); $index++) {
            $date = CarbonImmutable::parse($rows[$index]['due_date']);
            $discount /= 1 + (empty($rows[$index]['is_prepayment']) ? $terms->rateFor($date, $previous) : 0);
            $previous = $date;

            if ($this->isFixed($rows[$index])) {
                $extraordinaryValue += $rows[$index]['amount'] * $discount;
            } else {
                $ordinaryFactor += $discount;
            }
        }

        // Only extraordinary quotas are left: the last one closes the balance
        if ($ordinaryFactor === 0.0) {
            return $rows;
        }

        $remaining = $this->openingBalance($rows, $from, $terms) - $extraordinaryValue;

        if ($strict && $remaining <= 0) {
            throw new ScheduleException('Las cuotas extraordinarias cubren todo el saldo: reduce su valor o el número de cuotas.');
        }

        $quota = max(0, (int) round($remaining / $ordinaryFactor));

        for ($index = $from; $index < count($rows); $index++) {
            if (! $this->isFixed($rows[$index])) {
                $rows[$index]['amount'] = $quota;
            }
        }

        return $rows;
    }

    /**
     * Extraordinary quotas and extra payments keep their amount when the others are spread.
     *
     * @param  array<string, mixed>  $row
     */
    private function isFixed(array $row): bool
    {
        return ! empty($row['is_extraordinary']) || ! empty($row['is_prepayment']);
    }

    /**
     * Value for the quotas added at the end: the first ordinary quota after the edited one, or else the
     * last ordinary one before it.
     *
     * @param  list<array<string, mixed>>  $rows
     */
    private function followingQuota(array $rows, int $index): int
    {
        $ordinary = fn (array $row) => ! $this->isFixed($row) && $row['amount'] > 0;

        $after = array_values(array_filter(array_slice($rows, $index + 1), $ordinary));
        $before = array_values(array_filter(array_slice($rows, 0, $index), $ordinary));

        return (int) ($after[0]['amount'] ?? end($before)['amount'] ?? $rows[$index]['amount']);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function nextDueDate(array $rows, ScheduleTerms $terms): CarbonImmutable
    {
        if ($rows === []) {
            return $terms->firstDueDate;
        }

        $next = $terms->dueDateAfter(CarbonImmutable::parse(end($rows)['due_date']), 1);

        // Month-based schedules keep the day of the month they started on (31, 28, 31, 30...)
        if (! in_array($terms->frequency, [PaymentFrequency::WEEKLY, PaymentFrequency::BIWEEKLY], true)) {
            $day = CarbonImmutable::parse($rows[0]['due_date'])->day;
            $next = $next->day(min($day, $next->daysInMonth));
        }

        return $next;
    }

    /**
     * @return array<string, mixed>
     */
    private function newRow(ScheduleTerms $terms, CarbonImmutable $date, int $amount = 0): array
    {
        $isExtraordinary = $terms->isExtraordinary($date);

        return [
            'number' => 0,
            'due_date' => $date->toDateString(),
            'amount' => $isExtraordinary ? $terms->extraordinaryAmount : $amount,
            'interest' => 0,
            'principal' => 0,
            'balance' => 0,
            'is_extraordinary' => $isExtraordinary,
            'is_prepayment' => false,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function openingBalance(array $rows, int $from, ScheduleTerms $terms): int
    {
        return $from === 0 ? $terms->principal : (int) $rows[$from - 1]['balance'];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function previousDueDate(array $rows, int $from): ?CarbonImmutable
    {
        return $from === 0 ? null : CarbonImmutable::parse($rows[$from - 1]['due_date']);
    }

    private function ensurePrincipal(ScheduleTerms $terms): void
    {
        if ($terms->principal <= 0) {
            throw new ScheduleException('No hay saldo por financiar: la cuota inicial ya cubre el valor de la promesa.');
        }
    }
}
