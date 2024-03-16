<?php

namespace App\Livewire\Deed\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Escrituras')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.deed.index.page');
    }
}
