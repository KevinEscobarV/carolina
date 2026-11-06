<?php

namespace App\Livewire\Public\Buyer\Statement;

use App\Models\Buyer;
use App\Models\Promise;
use App\Models\Scopes\CategoryScope;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Lets a buyer find their promises by document number and download each account statement.
 * Used on the homepage and on the account statement page.
 */
class Lookup extends Component
{
    public string $document_number = '';

    /**
     * @var list<array{id: int, number: string, project: ?string, date: string, value: string, url: string}>
     */
    public array $promises = [];

    public bool $searched = false;

    public function explore(): void
    {
        $this->validate(
            ['document_number' => ['required', 'string', 'max:30']],
            attributes: ['document_number' => 'número de documento'],
        );

        $this->searched = true;

        // Buyers and promises of every campaign, even when someone is signed in
        $buyerIds = Buyer::withoutGlobalScope(CategoryScope::class)
            ->byDocument($this->document_number)
            ->pluck('id')
            ->all();

        if ($buyerIds === []) {
            $this->promises = [];

            return;
        }

        // The same person can be registered once per category, so gather the promises of every record
        $buyersOfAnyCategory = fn ($query) => $query->withoutGlobalScope(CategoryScope::class);
        $this->promises = Promise::withoutGlobalScope(CategoryScope::class)
            ->whereHas('buyers', fn ($query) => $buyersOfAnyCategory($query)->whereKey($buyerIds))
            ->with(['buyers' => fn ($query) => $buyersOfAnyCategory($query)->select('buyers.id'), 'category:id,name'])
            ->orderByDesc('signature_date')
            ->get()
            ->map(fn (Promise $promise) => [
                'id' => $promise->id,
                'number' => (string) $promise->number,
                'project' => $promise->category?->name,
                'date' => $promise->signature_date ? $promise->signature_date->translatedFormat('d \d\e F \d\e Y') : 'Sin fecha',
                'value' => $promise->value_formatted,
                // Signed links, so a statement cannot be downloaded by guessing ids
                'url' => url(url()->temporarySignedRoute(
                    'statement-download',
                    now()->addMinutes(30),
                    [$promise->id, $promise->buyers->whereIn('id', $buyerIds)->first()->id],
                    false
                )),
            ])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.public.buyer.statement.lookup');
    }
}
