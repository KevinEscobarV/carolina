<?php

namespace App\Livewire\Block\Index;

use App\Livewire\Forms\BlockForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public BlockForm $form;

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Bloque creado exitosamente',
                'El bloque se ha creado correctamente'
            );

            $this->dispatch('refresh-block-table');
        }
    }

    public function render()
    {
        return view('livewire.block.index.create');
    }
}
