<?php

namespace App\Livewire\Parcel\Index;

use App\Livewire\Forms\ParcelForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    public ParcelForm $form;

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Lote creado exitosamente',
                'El lote se ha creado correctamente'
            );

            $this->dispatch('refresh-parcel-table');
        }
    }

    public function render()
    {
        return view('livewire.parcel.index.create');
    }
}
