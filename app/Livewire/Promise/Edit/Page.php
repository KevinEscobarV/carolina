<?php

namespace App\Livewire\Promise\Edit;

use App\Enums\RebalanceMode;
use App\Livewire\Forms\PromiseForm;
use App\Livewire\Traits\EditsSchedule;
use App\Models\Parcel;
use App\Models\Promise;
use App\Services\Amortization\Coverage;
use App\Services\Amortization\ScheduleException;
use App\Services\Amortization\ScheduleTerms;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Editar promesa')]
class Page extends Component
{
    use Actions, EditsSchedule;

    public $block = null;

    public Promise $promise;

    public PromiseForm $form;

    public $isCreate = false;

    public function updatedFormParcels($parcels): void
    {
        $this->form->value = Parcel::whereIn('id', $parcels)->sum('value');
    }

    public function mount(): void
    {
        $this->form->setModel($this->promise);
        $this->schedule = $this->promise->scheduleRows();
    }

    /**
     * Generates the schedule again, keeping the quotas already paid.
     */
    public function project(): void
    {
        $paid = $this->lockedQuotas();

        try {
            $this->schedule = $this->form->generateSchedule(
                array_slice($this->schedule, 0, $paid),
                $paid > 0 ? ($this->schedule[$paid]['due_date'] ?? null) : null,
            );
            $this->scheduleVersion++;
            $this->resetErrorBag('schedule');
        } catch (ScheduleException $exception) {
            $this->addError('schedule', $exception->getMessage());
        }
    }

    public function save(): void
    {
        try {
            $this->form->update($this->schedule);
        } catch (ScheduleException $exception) {
            $this->addError('schedule', $exception->getMessage());

            return;
        }

        $this->notification()->confirm([
            'icon' => 'success',
            'title' => 'Promesa actualizada exitosamente',
            'description' => 'La promesa se ha actualizado correctamente',
            'accept' => [
                'label' => 'Volver a promesas',
                'url' => route('promises'),
            ],
            'rejectLabel' => 'Permanecer aquí',
        ]);
    }

    public function coverage(): Coverage
    {
        return Coverage::of($this->schedule, (float) $this->promise->payments()->where('is_initial_fee', false)->sum('paid_amount'));
    }

    protected function lockedQuotas(): int
    {
        return $this->coverage()->paidCount;
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
        return view('livewire.promise.edit.page');
    }
}
