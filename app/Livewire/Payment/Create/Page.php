<?php

namespace App\Livewire\Payment\Create;

use App\Livewire\Forms\PaymentForm;
use App\Models\Promise;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Crear pago')]
class Page extends Component
{
    use Actions;

    public PaymentForm $form;

    public function mount(): void
    {
        $this->form->payment_date = now()->format('Y-m-d');
        $this->form->agreement_date = now()->format('Y-m-d');
    }

    public function updatedFormPromiseId($promiseId): void
    {
        if ($promiseId) {
            $this->autocompleteFields((int) $promiseId);
        }
    }

    public function updatedFormIsInitialFee(): void
    {
        if ($this->form->promise_id) {
            $this->autocompleteFields((int) $this->form->promise_id);
        }
    }

    /**
     * Fills the payment with what the promise expects now: its initial fee, or what is left of the
     * quota it is on by the money already paid. Also shows the promise's schedule beside the form.
     */
    public function autocompleteFields(int $promiseId, bool $notify = true): void
    {
        $promise = Promise::with('payments', 'quotas')->find($promiseId);

        if (! $promise) {
            return;
        }

        $this->dispatch('show-amortization', $promise->id);

        if ($this->form->is_initial_fee) {
            if ($promise->payments->where('is_initial_fee', true)->isNotEmpty()) {
                $this->notification()->warning(
                    'Cuota inicial ya registrada',
                    'La cuota inicial de esta promesa ya ha sido registrada'
                );

                $this->form->is_initial_fee = false;

                return;
            }

            $this->form->agreement_amount = $promise->initial_fee;
            $this->form->agreement_date = $promise->signature_date;
            $this->form->paid_amount = $promise->initial_fee;
            $this->form->payment_date = $promise->signature_date;
        } else {
            $standing = $promise->standing();

            if (! $standing->currentQuota) {
                $this->form->agreement_date = now()->format('Y-m-d');

                if ($notify) {
                    $this->notification()->info('Promesa al día', 'Los pagos ya cubren todas las cuotas de la proyección.');
                }

                return;
            }

            $this->form->agreement_date = $standing->currentQuota['due_date'];
            $this->form->agreement_amount = $standing->currentQuota['amount'];
            $this->form->paid_amount = $standing->currentPending;
        }

        if ($notify) {
            $this->notification()->info(
                'Se han autocompletado los campos',
                'Recuerda revisar los datos antes de guardar el pago'
            );
        }
    }

    public function save(): void
    {
        $promiseId = $this->form->promise_id;

        if (! $this->form->save()) {
            return;
        }

        $this->notification()->success(
            'Pago creado exitosamente',
            'El pago ha sido registrado correctamente'
        );

        $this->dispatch('refresh-payment-table');

        // Keep the promise chosen: its schedule refreshes and the next quota is suggested
        $this->form->promise_id = $promiseId;
        $this->form->payment_date = now()->format('Y-m-d');
        $this->autocompleteFields((int) $promiseId, notify: false);
    }

    public function render(): View
    {
        return view('livewire.payment.create.page');
    }
}
