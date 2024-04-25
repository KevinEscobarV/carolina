<?php

namespace App\Livewire\User\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Usuarios')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.user.index.page');
    }
}
