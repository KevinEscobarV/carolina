<?php

namespace App\Livewire\Deed\Index;

use App\Livewire\Forms\DeedForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;
    
    public DeedForm $form;

    public $block;

    public function mount()
    {
        $this->form->signature_date = now();
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Escritura creada exitosamente',
                'La escritura se ha creado correctamente'
            );

            $this->dispatch('refresh-deed-table');
        }
    }

    public function render()
    {
        return view('livewire.deed.index.create');
    }
}
