<?php

namespace App\Livewire\Public\Buyer\Statement;

use App\Models\Buyer;
use App\Models\Promise;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;

class Page extends Component
{
    use Actions;

    #[Url('document')]
    public $document_number = '';

    public $promises = [];
    public $statementUrls = [];

    public function explore()
    {
        $buyers = Buyer::byDocument($this->document_number)->get();

        if ($buyers->isEmpty()) {
            $this->dialog()->error(
                'Ups !!!',
                'No se encontró ningún comprador con el número de documento ingresado.'
            );
            $this->promises = [];
            $this->statementUrls = [];
            return;
        }

        // The same person can be registered once per category, so gather the promises of every record
        $buyerIds = $buyers->modelKeys();
        $this->promises = Promise::whereHas('buyers', fn ($query) => $query->whereKey($buyerIds))
            ->with('buyers:id')
            ->get();

        // Signed links, so a statement cannot be downloaded by guessing ids
        $this->statementUrls = $this->promises->mapWithKeys(fn (Promise $promise) => [
            $promise->id => url(url()->temporarySignedRoute(
                'statement-download',
                now()->addMinutes(30),
                [$promise->id, $promise->buyers->whereIn('id', $buyerIds)->first()->id],
                false
            )),
        ])->all();
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.public.buyer.statement.page');
    }
}
