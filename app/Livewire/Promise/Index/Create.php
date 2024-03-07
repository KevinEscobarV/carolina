<?php

namespace App\Livewire\Promise\Index;

use App\Livewire\Forms\PromiseForm;
use App\Models\Parcel;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    public PromiseForm $form;

    public $block;

    public function updatedFormParcels($parcels): void
    {
        $this->form->value = Parcel::whereIn('id', $parcels)->sum('value');
    }

    public function mount()
    {
        $this->form->signature_date = now();
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Promesa creada exitosamente',
                'La promesa se ha creado correctamente'
            );

            $this->dispatch('refresh-promise-table');
        }
    }

    public function render()
    {
        return view('livewire.promise.index.create');
    }
}
