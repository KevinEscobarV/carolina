<?php

namespace App\Livewire\Block\Index;

use App\Livewire\Forms\BlockForm;
use Livewire\Component;

class Create extends Component
{
    public BlockForm $form;

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-block-table');
    }

    public function render()
    {
        return view('livewire.block.index.create');
    }
}
