<?php

namespace App\Livewire\Block\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manzanas')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.block.index.page');
    }
}
