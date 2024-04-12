<?php

namespace App\Livewire\Payment\Edit;

use App\Livewire\Forms\PaymentForm;
use App\Models\Payment;
use App\Models\Promise;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Editar pago')]
class Page extends Component
{
    use Actions;
    
    public Payment $payment;

    public PaymentForm $form;

    public function mount()
    {
        $this->form->setPayment($this->payment);
    }

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
            $initial_fee_payment = $promise->payments()->where('is_initial_fee', true)->where('id', '!=', $this->payment->id)->first();

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

    public function save()
    {
        $save = $this->form->update();

        if ($save) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Pago actualizado exitosamente',
                'description' => 'El pago ha sido actualizado correctamente',
                'accept'      => [
                    'label' => 'Volver a pagos',
                    'url' => route('payments'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.payment.edit.page');
    }
}
