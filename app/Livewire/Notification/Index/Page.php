<?php

namespace App\Livewire\Notification\Index;

use App\Models\Setting;
use App\Services\Labsmobile\SMS;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notificaciones'), Lazy()]
class Page extends Component
{
    public $balance = 0;
    public $price = 0;
    public $count = 0;

    public function mount()
    {
        $this->balance = SMS::balance();
        $this->price = SMS::prices();
        $this->count = Setting::firstOrCreate(
            ['key' => 'sms_count'],
            [
                'value' => 0,
                'description' => 'Cantidad de mensajes enviados'
            ]
        )->value;
    }

    public function placeholder()
    {
        return view('components.notification.placeholder');
    }

    public function render()
    {
        return view('livewire.notification.index.page');
    }
}
