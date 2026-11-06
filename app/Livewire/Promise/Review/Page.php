<?php

namespace App\Livewire\Promise\Review;

use App\Enums\PromiseStatus;
use App\Models\Promise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Promises of the campaign whose schedule needs a look: missing, not adding up to the amount
 * financed, dates out of order or quotas of $0. Each one links to its editor to fix it.
 */
#[Title('Revisión de proyecciones')]
class Page extends Component
{
    public const ISSUES = [
        'out_of_balance' => 'No cuadran con el saldo',
        'missing' => 'Sin proyección',
        'unordered_dates' => 'Fechas fuera de orden',
        'zero_quotas' => 'Cuotas en $0',
    ];

    #[Url]
    public string $issue = '';

    public function render(): View
    {
        $reviews = Promise::query()
            ->where(fn (Builder $query) => $query->whereNull('status')->orWhere('status', '!=', PromiseStatus::CANCELLED))
            ->with('quotas', 'buyers')
            ->get()
            ->map(fn (Promise $promise) => ['promise' => $promise, 'issues' => $promise->scheduleIssues()])
            ->filter(fn (array $review) => $review['issues'] !== [])
            ->sortByDesc(fn (array $review) => abs($review['promise']->scheduleDifference()))
            ->values();

        $counts = collect(self::ISSUES)->map(
            fn (string $label, string $type) => $reviews->filter(fn (array $review) => in_array($type, array_column($review['issues'], 'type'), true))->count()
        );

        return view('livewire.promise.review.page', [
            'reviews' => $this->issue === ''
                ? $reviews
                : $reviews->filter(fn (array $review) => in_array($this->issue, array_column($review['issues'], 'type'), true))->values(),
            'counts' => $counts,
            'total' => $reviews->count(),
        ]);
    }
}
