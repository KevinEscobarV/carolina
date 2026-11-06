<?php

namespace App\Livewire\Traits;

use App\Enums\RebalanceMode;
use App\Services\Amortization\ScheduleCalculator;
use App\Services\Amortization\ScheduleException;
use App\Services\Amortization\ScheduleTerms;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;

/**
 * Editing of an amortization schedule held in $schedule: changing the amount or the date of a quota,
 * marking it extraordinary, adding or removing quotas. Every change goes through the schedule
 * calculator, so the following quotas and the balances stay right. The leading quotas already paid
 * cannot change.
 */
trait EditsSchedule
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $schedule = [];

    /**
     * Grows when quotas are added or removed, so the inputs of the table are rebuilt.
     */
    public int $scheduleVersion = 0;

    /**
     * @var list<array<string, mixed>>
     */
    private array $scheduleBeforeEdit = [];

    abstract protected function scheduleTerms(): ScheduleTerms;

    abstract protected function scheduleMode(): RebalanceMode;

    /**
     * Number of leading quotas already paid, which cannot change.
     */
    protected function lockedQuotas(): int
    {
        return 0;
    }

    public function updatingSchedule(mixed $value, string $key): void
    {
        $this->scheduleBeforeEdit = $this->schedule;
    }

    public function updatedSchedule(mixed $value, string $key): void
    {
        [$index, $field] = array_pad(explode('.', $key, 2), 2, null);
        $index = (int) $index;

        $this->changeSchedule(function () use ($index, $field, $value) {
            $this->ensureNotPaid($index);

            $rows = $this->scheduleBeforeEdit;

            return match ($field) {
                'amount' => $this->calculator()->rebalance(
                    $this->withAmount($rows, $index, $value), $index, $this->scheduleTerms(), $this->scheduleMode()
                ),
                'due_date' => $this->calculator()->moveDueDate(
                    $rows, $index, CarbonImmutable::parse((string) $value), $this->scheduleTerms()
                ),
                default => $rows,
            };
        }, previous: $this->scheduleBeforeEdit);
    }

    public function toggleExtraordinary(int $index): void
    {
        $this->changeSchedule(function () use ($index) {
            $this->ensureNotPaid($index);
            $rows = $this->schedule;
            $rows[$index]['is_extraordinary'] = ! $rows[$index]['is_extraordinary'];

            return $rows;
        });
    }

    public function addQuota(): void
    {
        $this->changeSchedule(fn () => $this->calculator()->addQuota($this->schedule, $this->lockedQuotas(), $this->scheduleTerms()));
    }

    public function removeQuota(int $index): void
    {
        $this->changeSchedule(function () use ($index) {
            $this->ensureNotPaid($index);

            return $this->calculator()->removeQuota($this->schedule, $index, $this->lockedQuotas(), $this->scheduleTerms());
        });
    }

    /**
     * Turns the money paid beyond what is due today into an extra payment, and adapts the following
     * quotas as the promise chose ("Reproyectar saldo").
     */
    public function applyPrepayment(): void
    {
        $this->changeSchedule(fn () => $this->calculator()->applyPrepayment(
            $this->schedule,
            $this->paidTowardsQuotas(),
            CarbonImmutable::today(),
            $this->scheduleTerms(),
            $this->scheduleMode(),
        ));
    }

    /**
     * Money already paid towards the quotas (the initial fee apart).
     */
    protected function paidTowardsQuotas(): float
    {
        return 0;
    }

    /**
     * Makes the pending quotas pay off exactly the balance, spreading the difference over them.
     */
    public function fitSchedule(): void
    {
        $this->changeSchedule(fn () => $this->calculator()->redistribute($this->schedule, $this->lockedQuotas(), $this->scheduleTerms()));
    }

    /**
     * @param  callable(): list<array<string, mixed>>  $change
     * @param  list<array<string, mixed>>|null  $previous
     */
    private function changeSchedule(callable $change, ?array $previous = null): void
    {
        $previous ??= $this->schedule;

        try {
            $this->schedule = array_values($change());
            $this->resetErrorBag('schedule');

            if (count($this->schedule) !== count($previous)) {
                $this->scheduleVersion++;
            }
        } catch (ScheduleException|InvalidFormatException $exception) {
            $this->schedule = $previous;
            $this->scheduleVersion++;
            $this->addError('schedule', $exception instanceof ScheduleException ? $exception->getMessage() : 'La fecha no es válida.');
        }
    }

    private function ensureNotPaid(int $index): void
    {
        if ($index < $this->lockedQuotas()) {
            throw new ScheduleException('La cuota '.($index + 1).' ya está pagada y no se puede cambiar.');
        }
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function withAmount(array $rows, int $index, mixed $value): array
    {
        $amount = (int) round((float) $value);

        if ($amount < 0) {
            throw new ScheduleException('El valor de la cuota no puede ser negativo.');
        }

        $rows[$index]['amount'] = $amount;

        return $rows;
    }

    private function calculator(): ScheduleCalculator
    {
        return app(ScheduleCalculator::class);
    }
}
