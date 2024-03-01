<?php

namespace App\Livewire\Parcel\Index;

use App\Livewire\Forms\ParcelForm;
use Livewire\Component;

class Create extends Component
{
    public ParcelForm $form;

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-parcel-table');
    }

    public function render()
    {
        return view('livewire.parcel.index.create');
    }
}
