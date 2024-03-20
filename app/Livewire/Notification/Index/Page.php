<?php

namespace App\Livewire\Notification\Index;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notificaciones')]
class Page extends Component
{
    public function render()
    {
        return view('livewire.notification.index.page');
    }
}
