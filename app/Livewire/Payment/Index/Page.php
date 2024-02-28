<?php

namespace App\Livewire\Payment\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pagos')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.payment.index.page');
    }
}
