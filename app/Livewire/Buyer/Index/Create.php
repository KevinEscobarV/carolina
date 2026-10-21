<?php

namespace App\Livewire\Buyer\Index;

use App\Livewire\Forms\BuyerForm;
use App\Models\Buyer;
use App\Models\Scopes\CategoryScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public BuyerForm $form;

    public $copyFrom;

    /**
     * Prefill the form with a buyer registered in another category.
     */
    public function updatedCopyFrom($value): void
    {
        if (! $value) {
            return;
        }

        abort_unless(Auth::user()->can('change.category'), 403);

        $buyer = Buyer::withoutGlobalScope(CategoryScope::class)->findOrFail($value);

        $this->form->fillFrom($buyer);
        $this->resetValidation();

        $this->notification()->info(
            'Datos copiados',
            'Revisa la información y ajusta lo necesario antes de guardar'
        );
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->reset('copyFrom');

            $this->notification()->success(
                'Cliente creado exitosamente',
                'El cliente se ha creado correctamente'
            );

            $this->dispatch('refresh-buyer-table');
        }
    }

    public function render()
    {
        return view('livewire.buyer.index.create');
    }
}
