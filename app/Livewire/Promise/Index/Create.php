<?php

namespace App\Livewire\Promise\Index;

use App\Livewire\Forms\PromiseForm;
use Livewire\Component;

class Create extends Component
{
    public PromiseForm $form;

    public function mount()
    {
        $this->form->signature_date = now();
    }

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-promise-table');
    }

    public function render()
    {
        return view('livewire.promise.index.create');
    }
}
