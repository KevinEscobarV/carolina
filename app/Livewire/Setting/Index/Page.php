<?php

namespace App\Livewire\Setting\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Configuración')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.setting.index.page');
    }
}
