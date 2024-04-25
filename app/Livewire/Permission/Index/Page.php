<?php

namespace App\Livewire\Permission\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Roles y permisos')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.permission.index.page');
    }
}
