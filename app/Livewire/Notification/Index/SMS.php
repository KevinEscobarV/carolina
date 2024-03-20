<?php

namespace App\Livewire\Notification\Index;

use App\Models\Buyer;
use App\Services\Labsmobile\SMS as LabsmobileSMS;
use Livewire\Component;
use WireUi\Traits\Actions;

class SMS extends Component
{
    use Actions;

    public $message = '';
    public $buyers = [];

    public function sendSMS()
    {
        $this->validate([
            'message' => 'required',
            'buyers' => 'required'
        ]);

        $to = Buyer::whereIn('id', $this->buyers)->pluck('phone_one')->toArray();

        LabsmobileSMS::send($to, $this->message);

        $this->notification()->success(
            'SMS enviado correctamente',
            'Los mensajes se han enviado correctamente'
        );

        $this->reset(['message', 'buyers']);
    }

    public function render()
    {
        return view('livewire.notification.index.s-m-s');
    }
}
