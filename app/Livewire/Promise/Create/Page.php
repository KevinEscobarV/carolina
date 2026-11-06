<?php

namespace App\Livewire\Promise\Create;

use App\Enums\RebalanceMode;
use App\Livewire\Forms\PromiseForm;
use App\Livewire\Traits\EditsSchedule;
use App\Models\Parcel;
use App\Models\Setting;
use App\Services\Amortization\ScheduleException;
use App\Services\Amortization\ScheduleTerms;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Crear promesa')]
class Page extends Component
{
    use Actions, EditsSchedule;

    public PromiseForm $form;

    public $block;

    public $isCreate = true;

    public function updatedFormParcels($parcels): void
    {
        $this->form->value = Parcel::whereIn('id', $parcels)->sum('value');
    }

    public function mount(): void
    {
        $this->form->signature_date = now()->toDateString();
    }

    public function project(): void
    {
        try {
            $this->schedule = $this->form->generateSchedule();
            $this->scheduleVersion++;
            $this->resetErrorBag('schedule');
        } catch (ScheduleException $exception) {
            $this->addError('schedule', $exception->getMessage());
        }
    }

    public function save(): void
    {
        try {
            $this->form->save($this->schedule);
        } catch (ScheduleException $exception) {
            $this->addError('schedule', $exception->getMessage());

            return;
        }

        $this->schedule = [];
        $this->scheduleVersion++;

        $this->notification()->confirm([
            'icon' => 'success',
            'title' => 'Promesa creada exitosamente',
            'description' => 'La promesa se ha creado correctamente',
            'accept' => [
                'label' => 'Volver a tabla de promesas',
                'url' => route('promises'),
            ],
            'rejectLabel' => 'Permanecer aquí',
        ]);
    }

    protected function scheduleTerms(): ScheduleTerms
    {
        return $this->form->scheduleTerms();
    }

    protected function scheduleMode(): RebalanceMode
    {
        return RebalanceMode::fromSwitchPayment((bool) $this->form->switch_payment);
    }

    public function render(): View
    {
        return view('livewire.promise.create.page', [
            'usury' => (float) Setting::usuryRate()->value,
        ]);
    }
}
