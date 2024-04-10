<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Forms\PaymentForm;
use App\Models\Promise;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public PaymentForm $form;

    public function updatedFormPromiseId($value): void
    {
        $this->autocompleteFields($value);
    }

    public function updatedFormIsInitialFee(): void
    {
        if ($this->form->promise_id) {
            $this->autocompleteFields($this->form->promise_id);
        }
    }

    public function autocompleteFields($promise_id): void
    {
        $promise = Promise::find($promise_id);

        if ($this->form->is_initial_fee) {

            // Check if the promise has a initial fee payment
            $initial_fee_payment = $promise->payments()->where('is_initial_fee', true)->first();

            if ($initial_fee_payment) {
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
            $this->form->agreement_amount = $promise->quota_amount;
            $this->form->agreement_date = $promise->current_cut_off_date;
        }

        $this->notification()->info(
            'Se han autocompletado los campos',
            'Recuerda revisar los datos antes de guardar el pago'
        );
    }

    public function mount()
    {
        $this->form->payment_date = now();
        $this->form->agreement_date = now();
    }

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->success(
                'Pago creado exitosamente',
                'El pago ha sido registrado correctamente'
            );

            $this->dispatch('refresh-payment-table');
        }
    }

    public function render()
    {
        return view('livewire.payment.index.create');
    }
}
