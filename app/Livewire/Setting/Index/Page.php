<?php

namespace App\Livewire\Setting\Index;

use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Configuración')]
class Page extends Component
{
    use Actions;

    public $message_header = '';

    public $message_footer = '';

    public $grace_days = 0;

    public $usury_rate = '';

    public function mount(): void
    {
        $this->message_header = Setting::firstOrCreate(
            ['key' => 'message_header'],
            [
                'value' => config('sms.message_header'),
                'description' => 'Mensaje de cabecera para los SMS',
            ]
        )->value;

        $this->message_footer = Setting::firstOrCreate(
            ['key' => 'message_footer'],
            [
                'value' => config('sms.message_footer'),
                'description' => 'Mensaje de pie para los SMS',
            ]
        )->value;

        $this->grace_days = (int) $this->graceDaysSetting()->value;
        $this->usury_rate = Setting::usuryRate()->value;
    }

    public function save(): void
    {
        $this->validate([
            'message_header' => 'required',
            'message_footer' => 'required',
        ]);

        Setting::where('key', 'message_header')->update(['value' => $this->message_header]);
        Setting::where('key', 'message_footer')->update(['value' => $this->message_footer]);

        $this->notification()->success(
            'Configuración guardada correctamente',
            'Los mensajes de cabecera y pie para los SMS han sido guardados correctamente'
        );
    }

    public function saveArrears(): void
    {
        $this->validate([
            'grace_days' => ['required', 'integer', 'min:0', 'max:90'],
            'usury_rate' => ['required', 'numeric', 'gt:0', 'max:100'],
        ], [], [
            'grace_days' => 'días de gracia',
            'usury_rate' => 'tasa de usura',
        ]);

        $this->graceDaysSetting()->update(['value' => (string) (int) $this->grace_days]);

        // Saving it, even unchanged, records that someone checked the rate in force
        $usury = Setting::usuryRate();
        $usury->value = (string) (float) $this->usury_rate;
        $usury->isDirty() ? $usury->save() : $usury->touch();

        $this->notification()->success(
            'Configuración guardada correctamente',
            'Los días de gracia y la tasa de usura han sido actualizados'
        );
    }

    private function graceDaysSetting(): Setting
    {
        return Setting::firstOrCreate(
            ['key' => 'grace_days'],
            ['value' => '0', 'description' => 'Días después del vencimiento antes de considerar una cuota en mora'],
        );
    }

    public function render(): View
    {
        $usuryCheckedAt = Setting::usuryRate()->updated_at ?? Carbon::now();

        return view('livewire.setting.index.page', [
            'usuryCheckedAt' => $usuryCheckedAt,
            'usuryIsStale' => $usuryCheckedAt->lt(now()->subDays(35)),
        ]);
    }
}
