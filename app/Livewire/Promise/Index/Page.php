<?php

namespace App\Livewire\Promise\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Promesas')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.promise.index.page');
    }
}
