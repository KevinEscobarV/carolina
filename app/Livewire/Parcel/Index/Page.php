<?php

namespace App\Livewire\Parcel\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Lotes')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.parcel.index.page');
    }
}
