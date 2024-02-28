<?php

namespace App\Livewire\Buyer\Index;

use App\Livewire\Forms\BuyerForm;
use Livewire\Component;

class Create extends Component
{
    public BuyerForm $form;

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-buyer-table');
    }

    public function render()
    {
        return view('livewire.buyer.index.create');
    }
}
