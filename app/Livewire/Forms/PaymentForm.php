<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\Promise;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaymentForm extends Form
{
    public ?Payment $payment;

    #[Validate('required', 'promesa')]
    public $promise_id;

    #[Validate('required', 'número de factura')]
    public $bill_number;

    #[Validate('required|date', 'fecha de acuerdo')]
    public $agreement_date;

    #[Validate('required|numeric', 'monto')]
    public $agreement_amount;

    #[Validate('required|date', 'fecha de pago')]
    public $payment_date;

    #[Validate('required|numeric', 'valor pagado')]
    public $paid_amount;

    #[Validate('nullable', 'banco')]
    public $bank;

    #[Validate('required', 'método de pago')]
    public $payment_method = PaymentMethod::CASH;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    #[Validate('required|boolean', 'es cuota inicial')]
    public $is_initial_fee = false;

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
        $this->fill($payment->only([
            'promise_id',
            'bill_number',
            'agreement_amount',
            'paid_amount',
            'bank',
            'payment_method',
            'observations',
            'is_initial_fee',
        ]));

        $this->agreement_date = $payment->agreement_date->format('Y-m-d');
        $this->payment_date = $payment->payment_date->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        // Validate if the promise has an initial fee payment
        if ($this->is_initial_fee) {
            $promise = Promise::find($this->promise_id);

            $initialFeePayment = $promise->payments()->where('is_initial_fee', true)->first();

            if ($initialFeePayment) {
                $this->addError('is_initial_fee', 'La cuota inicial de esta promesa ya ha sido registrada');

                return false;
            } else if ($this->paid_amount != $promise->initial_fee) {
                $this->addError('paid_amount', 'El monto pagado debe ser igual al monto de la cuota inicial indicada en la promesa, por favor no modifiques este campo después de haber marcado la casilla de cuota inicial');

                return false;
            }
        }

        Payment::create($this->all());

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        // Validate if the promise has an initial fee payment
        if ($this->is_initial_fee) {
            $promise = $this->payment->promise;

            $initialFeePayment = $promise->payments()->where('is_initial_fee', true)->where('id', '!=', $this->payment->id)->first();

            if ($initialFeePayment) {
                $this->addError('is_initial_fee', 'La cuota inicial de esta promesa ya ha sido registrada');

                return false;
            } else if ($this->paid_amount != $promise->initial_fee) {
                $this->addError('paid_amount', 'El monto pagado debe ser igual al monto de la cuota inicial indicada en la promesa, por favor no modifiques este campo después de haber marcado la casilla de cuota inicial');

                return false;
            }
        }

        $this->payment->update($this->all());

        return true;
    }
}
