<?php

namespace App\Livewire\Buyer\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Compradores')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.buyer.index.page');
    }
}
