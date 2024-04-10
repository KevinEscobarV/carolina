<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentMethod;
use App\Models\Payment;
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

    #[Validate('nullable|boolean', 'es cuota inicial')]
    public $is_initial_fee;

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
        $this->fill($payment->only([
            'promise_id',
            'bill_number',
            'agreement_date',
            'agreement_amount',
            'payment_date',
            'paid_amount',
            'bank',
            'payment_method',
            'observations',
            'is_initial_fee',
        ]));
    }

    public function save()
    {
        $this->validate();

        Payment::create($this->all());

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        $this->payment->update($this->all());

        return true;
    }
}
