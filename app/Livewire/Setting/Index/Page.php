<?php

namespace App\Livewire\Setting\Index;

use App\Models\Setting;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Configuración')]
class Page extends Component
{
    use Actions;

    public $message_header = '';

    public $message_footer = '';

    public function mount()
    {
        $header = Setting::firstOrCreate(
            ['key' => 'message_header'],
            [
                'value' => config('sms.message_header'),
                'description' => 'Mensaje de cabecera para los SMS'
            ]
        );

        $footer = Setting::firstOrCreate(
            ['key' => 'message_footer'],
            [
                'value' => config('sms.message_footer'),
                'description' => 'Mensaje de pie para los SMS'
            ]
        );

        $this->message_header = $header->value;
        $this->message_footer = $footer->value;
    }

    public function save()
    {
        $this->validate([
            'message_header' => 'required',
            'message_footer' => 'required',
        ]);

        Setting::where('key', 'message_header')->update(['value' => $this->message_header]);
        Setting::where('key', 'message_footer')->update(['value' => $this->message_footer]);

        $this->notification()->success(
            "Configuración guardada correctamente",
            "Los mensajes de cabecera y pie para los SMS han sido guardados correctamente"
        );
    }

    public function render()
    {
        return view('livewire.setting.index.page');
    }
}
