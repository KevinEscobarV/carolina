<?php

namespace App\Livewire\Buyer\Index;

use App\Livewire\Forms\BuyerForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    
    public BuyerForm $form;

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
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
