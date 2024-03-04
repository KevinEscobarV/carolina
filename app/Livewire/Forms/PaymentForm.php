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
    public $amount;

    #[Validate('required|date', 'fecha de pago')]
    public $payment_date;

    #[Validate('required|numeric', 'valor pagado')]
    public $paid_amount;

    #[Validate('required', 'método de pago')]
    public PaymentMethod $payment_method = PaymentMethod::CASH;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    public function setBuyer(Payment $payment): void
    {
        $this->payment = $payment;
        $this->fill($payment->only([
            'agreement_date',
            'amount',
            'payment_date',
            'paid_amount',
            'payment_method',
            'observations',
        ]));
    }

    public function save(): void
    {
        $this->validate();

        Payment::create($this->all());
    }

    public function update(): void
    {
        $this->validate();

        $this->payment->update($this->all());
    }
}
