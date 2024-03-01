<?php

namespace App\Livewire\Category\Index;

use App\Livewire\Forms\CategoryForm;
use Livewire\Component;

class Create extends Component
{
    public CategoryForm $form;

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-category-table');
    }
    
    public function render()
    {
        return view('livewire.category.index.create');
    }
}
