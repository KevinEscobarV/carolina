<?php

namespace App\Livewire\Category\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Campañas')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.category.index.page');
    }
}
