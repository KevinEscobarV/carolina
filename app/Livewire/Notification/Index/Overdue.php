<?php

namespace App\Livewire\Notification\Index;

use App\Livewire\Traits\Sortable;
use App\Models\Promise;
use App\Models\Setting;
use App\Services\Labsmobile\SMS as LabsmobileSMS;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class Overdue extends Component
{
    use Actions;
    use WithPagination;
    use Sortable;

    public $selectedIds = [];
    public $idsOnPage = [];

    public function mount()
    {
        $this->perPage = '20';
    }

    public function selectAll()
    {
        $this->selectedIds = $this->promises()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function sendSelected()
    {
        $this->validate([
            'selectedIds' => 'required|array|min:1'
        ]);

        $promises = Promise::whereIn('id', $this->selectedIds)->with('buyers')->get();

        $reponses = 'Respuestas: LabsmobileSMS: <br>';

        foreach ($promises as $promise) {
            $message_header = Setting::firstOrCreate(
                ['key' => 'message_header'],
                [
                    'value' => config('sms.message_header'),
                    'description' => 'Mensaje de cabecera para los SMS'
                ]
            )->value;
    
            $message_footer = Setting::firstOrCreate(
                ['key' => 'message_footer'],
                [
                    'value' => config('sms.message_footer'),
                    'description' => 'Mensaje de pie para los SMS'
                ]
            )->value;

            $message = $message_header . ' Fecha:' . Carbon::parse($promise->current_quota['due_date'])->format('d/m/Y') . ', Dias mora:' . Carbon::parse($promise->current_quota['due_date'])->diffInDays(Carbon::now()->format('Y-m-d')) . '. ' . $message_footer;

            if ($promise->buyers->count() > 0) {
                $to = $promise->buyers->pluck('phone_one')->toArray();

                $send = LabsmobileSMS::send($to, $message);

                $reponses .= $send . '<br>';
            }
        }

        $this->notification()->info(
            'SMS enviado correctamente',
            'Los mensajes se han enviado correctamente: ' . $reponses
        );

        $this->reset(['selectedIds']);
    }

    public function promises(): object
    {
        $raw = config('database.default') === 'pgsql'
        ? "((projection->>(((SELECT COUNT(*) FROM payments WHERE payments.promise_id = promises.id and payments.is_initial_fee = '0') + 1)::int))::jsonb->>'due_date')::date < CURRENT_DATE"
        : "JSON_UNQUOTE(JSON_EXTRACT(`projection`, CONCAT('$[', ((SELECT COUNT(*) FROM `payments` WHERE `payments`.`promise_id` = `promises`.`id` AND `payments`.`is_initial_fee` = '0') + 1), '].due_date'))) < CURDATE()";

        return Promise::whereNotNull('projection')
            ->whereRaw($raw)
            ->sort($this->sortCol, $this->sortAsc)
            ->with('buyers', 'payments', 'parcels.block');
    }

    public function render()
    {
        $promises = $this->promises()->paginate($this->perPage);

        $this->idsOnPage = $promises->map(fn ($order) => (string) $order->id)->toArray();

        return view('livewire.notification.index.overdue', [
            'promises' => $promises
        ]);
    }
}
