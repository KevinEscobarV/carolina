<?php

namespace App\Services\Dashboard;

use App\Enums\PromiseStatus;
use App\Models\Parcel;
use App\Models\Payment;
use App\Models\Promise;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Figures of the dashboard for the current campaign: totals, monthly collections against what was
 * scheduled, arrears by age, payment methods, recent sales and the quotas due soon. Cancelled promises
 * are left out.
 */
class Overview
{
    public const MONTHS_BACK = 11;

    public const MONTHS_AHEAD = 6;

    /**
     * @var Collection<int, Promise>
     */
    private Collection $promises;

    private CarbonImmutable $today;

    public function __construct(?CarbonImmutable $today = null)
    {
        $this->today = $today ?? CarbonImmutable::today();
        $this->promises = Promise::query()
            ->where(fn (Builder $query) => $query->whereNull('status')->orWhere('status', '!=', PromiseStatus::CANCELLED))
            ->with('quotas', 'payments', 'buyers:id,names,surnames')
            ->get();
    }

    /**
     * @return array{sales: int, promises: int, collected: int, receivable: int, overdue: int, overduePromises: int, lotsSold: int, lots: int, thisMonth: int, lastMonth: int}
     */
    public function totals(): array
    {
        $payments = $this->payments();
        $month = $this->today->startOfMonth();
        $collectedIn = fn (CarbonImmutable $start) => (int) round($payments
            ->filter(fn (Payment $payment) => $payment->payment_date?->between($start, $start->endOfMonth()))
            ->sum('paid_amount'));

        $overdue = $this->promises->filter(fn (Promise $promise) => $promise->standing()->isOverdue());

        return [
            'sales' => (int) round($this->promises->sum('value')),
            'promises' => $this->promises->count(),
            'collected' => (int) round($payments->sum('paid_amount')),
            'receivable' => (int) round($this->promises->sum(fn (Promise $promise) => max(0, (float) $promise->value - (float) $promise->payments->sum('paid_amount')))),
            'overdue' => (int) $overdue->sum(fn (Promise $promise) => $promise->standing()->overdueAmount),
            'overduePromises' => $overdue->count(),
            'lotsSold' => Parcel::query()->whereNotNull('promise_id')->count(),
            'lots' => Parcel::query()->count(),
            'thisMonth' => $collectedIn($month),
            'lastMonth' => $collectedIn($month->subMonth()),
        ];
    }

    /**
     * Money collected and money scheduled (quotas due plus initial fees of the promises signed) per month,
     * from MONTHS_BACK months ago to MONTHS_AHEAD months ahead. Future months have nothing collected yet.
     *
     * @return array{labels: list<string>, months: list<string>, collected: list<int|null>, scheduled: list<int>, firstFuture: int}
     */
    public function monthly(): array
    {
        $current = $this->today->startOfMonth();
        $payments = $this->payments()->filter(fn (Payment $payment) => $payment->payment_date !== null);
        $quotas = $this->promises->flatMap(fn (Promise $promise) => $promise->quotas);

        $series = ['labels' => [], 'months' => [], 'collected' => [], 'scheduled' => [], 'firstFuture' => self::MONTHS_BACK + 1];

        for ($offset = -self::MONTHS_BACK; $offset <= self::MONTHS_AHEAD; $offset++) {
            $start = $current->addMonths($offset);
            $key = $start->format('Y-m');

            $series['labels'][] = $start->locale('es')->isoFormat('MMM YY');
            $series['months'][] = $start->locale('es')->isoFormat('MMMM [de] YYYY');
            $series['collected'][] = $offset > 0 ? null : (int) round($payments
                ->filter(fn (Payment $payment) => $payment->payment_date->format('Y-m') === $key)
                ->sum('paid_amount'));
            $series['scheduled'][] = (int) round(
                $quotas->filter(fn ($quota) => $quota->due_date->format('Y-m') === $key)->sum('amount')
                + $this->promises->filter(fn (Promise $promise) => $promise->signature_date?->format('Y-m') === $key)->sum('initial_fee')
            );
        }

        return $series;
    }

    /**
     * Promises with a schedule by how late they are, with the money overdue in each group.
     *
     * @return list<array{key: string, label: string, count: int, amount: int}>
     */
    public function aging(): array
    {
        $groups = [
            'up_to_date' => ['label' => 'Al día', 'count' => 0, 'amount' => 0],
            'up_to_30' => ['label' => '1 a 30 días', 'count' => 0, 'amount' => 0],
            'up_to_90' => ['label' => '31 a 90 días', 'count' => 0, 'amount' => 0],
            'over_90' => ['label' => 'Más de 90 días', 'count' => 0, 'amount' => 0],
        ];

        foreach ($this->promises->filter(fn (Promise $promise) => $promise->quotas->isNotEmpty()) as $promise) {
            $standing = $promise->standing();
            $key = match (true) {
                ! $standing->isOverdue() => 'up_to_date',
                $standing->daysOverdue <= 30 => 'up_to_30',
                $standing->daysOverdue <= 90 => 'up_to_90',
                default => 'over_90',
            };

            $groups[$key]['count']++;
            $groups[$key]['amount'] += $standing->overdueAmount;
        }

        return array_map(fn (string $key, array $group) => ['key' => $key, ...$group], array_keys($groups), $groups);
    }

    /**
     * Money collected by payment method over the last twelve months, the largest first.
     *
     * @return list<array{label: string, amount: int, share: float}>
     */
    public function paymentMethods(): array
    {
        $since = $this->today->startOfMonth()->subMonths(self::MONTHS_BACK);
        $payments = $this->payments()->filter(fn (Payment $payment) => $payment->payment_date?->gte($since));
        $total = max(1, (float) $payments->sum('paid_amount'));

        return $payments
            ->groupBy(fn (Payment $payment) => $payment->payment_method?->label() ?? 'Sin medio')
            ->map(fn (Collection $group, string $label) => [
                'label' => $label,
                'amount' => (int) round($group->sum('paid_amount')),
                'share' => round($group->sum('paid_amount') / $total * 100, 1),
            ])
            ->sortByDesc('amount')
            ->values()
            ->all();
    }

    /**
     * Promises signed per month over the last twelve months, this one included, with the totals of those
     * months and of the twelve before them.
     *
     * @return array{count: int, value: int, previousCount: int, previousValue: int, months: list<array{label: string, count: int, value: int}>}
     */
    public function sales(): array
    {
        $since = $this->today->startOfMonth()->subMonths(self::MONTHS_BACK);
        $signed = $this->promises->filter(fn (Promise $promise) => $promise->signature_date !== null);
        $recent = $signed->filter(fn (Promise $promise) => $promise->signature_date->gte($since));
        $previous = $signed->filter(fn (Promise $promise) => $promise->signature_date->gte($since->subYear()) && $promise->signature_date->lt($since));

        $months = [];
        for ($offset = 0; $offset <= self::MONTHS_BACK; $offset++) {
            $start = $since->addMonths($offset);
            $inMonth = $recent->filter(fn (Promise $promise) => $promise->signature_date->format('Y-m') === $start->format('Y-m'));
            $months[] = [
                'label' => $start->locale('es')->isoFormat('MMMM [de] YYYY'),
                'count' => $inMonth->count(),
                'value' => (int) round($inMonth->sum('value')),
            ];
        }

        return [
            'count' => $recent->count(),
            'value' => (int) round($recent->sum('value')),
            'previousCount' => $previous->count(),
            'previousValue' => (int) round($previous->sum('value')),
            'months' => $months,
        ];
    }

    /**
     * Quotas of promises up to date that fall due in the next $days days, the soonest first.
     *
     * @return array{items: list<array{promise: Promise, due_date: CarbonImmutable, amount: int}>, total: int, count: int}
     */
    public function upcoming(int $days = 30, int $limit = 6): array
    {
        $until = $this->today->addDays($days)->toDateString();

        $items = $this->promises
            ->map(fn (Promise $promise) => [$promise, $promise->standing()])
            ->filter(fn (array $pair) => ! $pair[1]->isOverdue() && $pair[1]->currentQuota && $pair[1]->currentQuota['due_date'] >= $this->today->toDateString() && $pair[1]->currentQuota['due_date'] <= $until)
            ->map(fn (array $pair) => [
                'promise' => $pair[0],
                'due_date' => CarbonImmutable::parse($pair[1]->currentQuota['due_date']),
                'amount' => $pair[1]->currentPending,
            ])
            ->sortBy(fn (array $item) => $item['due_date']->toDateString())
            ->values();

        return [
            'items' => $items->take($limit)->all(),
            'total' => (int) $items->sum('amount'),
            'count' => $items->count(),
        ];
    }

    /**
     * @return Collection<int, Payment>
     */
    private function payments(): Collection
    {
        return $this->promises->flatMap(fn (Promise $promise) => $promise->payments);
    }
}
