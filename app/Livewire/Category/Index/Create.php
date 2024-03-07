<?php

namespace App\Livewire\Category\Index;

use App\Livewire\Forms\CategoryForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public CategoryForm $form;

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Campaña creada exitosamente',
                'La campaña se ha creado correctamente'
            );

            $this->dispatch('refresh-category-table');
        }
    }
    
    public function render()
    {
        return view('livewire.category.index.create');
    }
}
