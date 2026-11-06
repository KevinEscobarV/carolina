<?php

namespace App\Livewire\Payment\Components;

use App\Enums\RebalanceMode;
use App\Livewire\Traits\EditsSchedule;
use App\Models\Promise;
use App\Services\Amortization\Coverage;
use App\Services\Amortization\ScheduleTerms;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\Actions;

/**
 * Schedule of the promise chosen in the payment form: what each quota got from the payments,
 * and editing of the pending quotas.
 */
class Amortization extends Component
{
    use Actions, EditsSchedule;

    public ?Promise $promise = null;

    #[On('show-amortization')]
    public function setProjection(Promise $promise): void
    {
        $this->promise = $promise;
        $this->schedule = $promise->scheduleRows();
        $this->scheduleVersion++;
        $this->resetErrorBag('schedule');
    }

    public function save(): void
    {
        $this->promise->saveSchedule($this->schedule);

        $this->notification()->success(
            'Proyección guardada',
            'Las cuotas de la promesa se actualizaron correctamente.'
        );
    }

    public function coverage(): Coverage
    {
        return Coverage::of($this->schedule, $this->paidTowardsQuotas());
    }

    protected function paidTowardsQuotas(): float
    {
        return (float) $this->promise->payments()->where('is_initial_fee', false)->sum('paid_amount');
    }

    protected function lockedQuotas(): int
    {
        return $this->coverage()->paidCount;
    }

    protected function scheduleTerms(): ScheduleTerms
    {
        return $this->promise->scheduleTerms();
    }

    protected function scheduleMode(): RebalanceMode
    {
        return $this->promise->rebalanceMode();
    }

    public function render(): View
    {
        return view('livewire.payment.components.amortization');
    }
}
