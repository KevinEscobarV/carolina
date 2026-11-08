<?php

namespace App\Services\Reports;

use App\Enums\PortfolioStatus;
use App\Enums\PromiseStatus;
use App\Models\Payment;
use App\Models\Promise;
use App\Services\Amortization\Standing;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * State of the portfolio of the current campaign on a cut-off date: one entry per promise signed by then
 * (cancelled ones left out), with the payments of its last payment day (often one receipt per quota paid),
 * what it has paid and owes, and how late it was. Promises without payments are included, as they are
 * usually the ones to collect.
 */
class PortfolioStatement
{
    /**
     * @var Collection<int, array{promise: Promise, lastPayments: Collection<int, Payment>, standing: Standing, paid: int, balance: int, status: PortfolioStatus}>
     */
    public readonly Collection $entries;

    public function __construct(public readonly CarbonImmutable $cutOff, public readonly bool $onlyLate = false)
    {
        $this->entries = Promise::query()
            ->where(fn (Builder $query) => $query->whereNull('status')->orWhere('status', '!=', PromiseStatus::CANCELLED))
            ->where(fn (Builder $query) => $query->whereNull('signature_date')->orWhereDate('signature_date', '<=', $cutOff->toDateString()))
            ->with('quotas', 'payments', 'buyers', 'parcels.block')
            ->get()
            ->sort(fn (Promise $first, Promise $second) => strnatcasecmp(trim($first->number), trim($second->number)))
            ->map(fn (Promise $promise) => $this->entry($promise))
            ->when($onlyLate, fn (Collection $entries) => $entries->filter(fn (array $entry) => $entry['status']->isOverdue()))
            ->values();
    }

    /**
     * Promises, balance and arrears per status, in the order of the statuses.
     *
     * @return list<array{status: PortfolioStatus, count: int, balance: int, overdue: int}>
     */
    public function summary(): array
    {
        return array_map(function (PortfolioStatus $status) {
            $entries = $this->entries->filter(fn (array $entry) => $entry['status'] === $status);

            return [
                'status' => $status,
                'count' => $entries->count(),
                'balance' => (int) $entries->sum('balance'),
                'overdue' => (int) $entries->sum(fn (array $entry) => $entry['standing']->overdueAmount),
            ];
        }, PortfolioStatus::cases());
    }

    /**
     * @return array{promise: Promise, lastPayments: Collection<int, Payment>, standing: Standing, paid: int, balance: int, status: PortfolioStatus}
     */
    private function entry(Promise $promise): array
    {
        $payments = $promise->paymentsUntil($this->cutOff);
        $paid = (int) round((float) $payments->sum('paid_amount'));
        $balance = max(0, (int) round((float) $promise->value) - $paid);
        $standing = $promise->standingAt($this->cutOff);
        $lastDate = $payments->last()?->payment_date?->toDateString();

        return [
            'promise' => $promise,
            'lastPayments' => $payments->filter(fn (Payment $payment) => $payment->payment_date?->toDateString() === $lastDate)->values(),
            'standing' => $standing,
            'paid' => $paid,
            'balance' => $balance,
            'status' => PortfolioStatus::of($standing, $promise->quotas->isNotEmpty(), $balance),
        ];
    }
}
