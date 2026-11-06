<?php

namespace App\Livewire\Notification\Index;

use App\Livewire\Traits\Sortable;
use App\Models\Promise;
use App\Models\Setting;
use App\Services\Labsmobile\SMS as LabsmobileSMS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

class Overdue extends Component
{
    use Actions;
    use Sortable;
    use WithPagination;

    public $selectedIds = [];

    public $idsOnPage = [];

    /**
     * @var array<int, int>|null
     */
    private ?array $overdueDays = null;

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
            'selectedIds' => 'required|array|min:1',
        ]);

        $promises = Promise::whereIn('id', $this->selectedIds)->with('buyers', 'quotas', 'payments')->get();

        $reponses = 'Respuestas: LabsmobileSMS: <br>';

        foreach ($promises as $promise) {
            $message_header = Setting::firstOrCreate(
                ['key' => 'message_header'],
                [
                    'value' => config('sms.message_header'),
                    'description' => 'Mensaje de cabecera para los SMS',
                ]
            )->value;

            $message_footer = Setting::firstOrCreate(
                ['key' => 'message_footer'],
                [
                    'value' => config('sms.message_footer'),
                    'description' => 'Mensaje de pie para los SMS',
                ]
            )->value;

            $standing = $promise->standing();

            // It may have been paid since the list was loaded
            if (! $standing->isOverdue()) {
                continue;
            }

            $message = $message_header.' Fecha:'.Carbon::parse($standing->currentQuota['due_date'])->format('d/m/Y').', Dias mora:'.$standing->daysOverdue.'. '.$message_footer;

            if ($promise->buyers->count() > 0) {
                $to = $promise->buyers->pluck('phone_one')->toArray();

                $send = LabsmobileSMS::send($to, $message);

                $reponses .= $send.'<br>';
            }
        }

        $this->notification()->info(
            'SMS enviado correctamente',
            'Los mensajes se han enviado correctamente: '.$reponses
        );

        $this->reset(['selectedIds']);
    }

    /**
     * Promises behind on their quotas by the money paid, the most overdue first unless sorted otherwise.
     */
    public function promises(): Builder
    {
        $overdue = $this->overdueDays();

        return Promise::query()
            ->whereKey(array_keys($overdue))
            ->when(
                $this->sortColumn(),
                fn (Builder $query) => $query->sort($this->sortColumn(), $this->sortAsc),
                fn (Builder $query) => $overdue ? $query->orderByRaw(
                    'CASE promises.id '.implode(' ', array_map(fn (int $id, int $position) => "WHEN {$id} THEN {$position}", array_keys($overdue), range(1, count($overdue)))).' END'
                ) : $query,
            )
            ->with('buyers', 'payments', 'quotas', 'parcels.block');
    }

    /**
     * @return array<int, int>
     */
    private function overdueDays(): array
    {
        return $this->overdueDays ??= Promise::overdueDays();
    }

    /**
     * @return array<int, string>
     */
    protected function sortableColumns(): array
    {
        return ['number', 'buyer', 'value', 'initial_fee', 'quota_amount', 'interest_rate', 'payment_frequency', 'payment_method'];
    }

    public function render()
    {
        $promises = $this->promises()->paginate($this->perPage);

        $this->idsOnPage = $promises->map(fn ($order) => (string) $order->id)->toArray();

        return view('livewire.notification.index.overdue', [
            'promises' => $promises,
        ]);
    }
}
