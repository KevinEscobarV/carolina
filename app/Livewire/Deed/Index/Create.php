<?php

namespace App\Livewire\Deed\Index;

use App\Livewire\Forms\DeedForm;
use Livewire\Component;

class Create extends Component
{
    public DeedForm $form;

    public $block_id;

    public function mount()
    {
        $this->form->signature_date = now();
    }

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-deed-table');
    }

    public function render()
    {
        return view('livewire.deed.index.create');
    }
}
