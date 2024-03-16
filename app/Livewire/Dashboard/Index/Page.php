<?php

namespace App\Livewire\Dashboard\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Panel administrativo')]
class Page extends Component
{
    public Filters $filters;

    public function mount()
    {
        $this->filters->init();
    }

    public function render()
    {
        return view('livewire.dashboard.index.page');
    }
}
