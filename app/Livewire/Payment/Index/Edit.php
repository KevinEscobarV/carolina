<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Forms\PaymentForm;
use App\Models\Payment;
use Livewire\Attributes\On;
use Livewire\Component;
use WireUi\Traits\Actions;

class Edit extends Component
{
    use Actions;

    public PaymentForm $state;

    public $open = false;
    
    #[On('edit-payment')]
    public function edit(Payment $payment)
    {
        $this->state->setPayment($payment);
        $this->open = true;
    }

    public function update()
    {
        $this->state->update();

        $this->notification()->success(
            'Pago actualizado exitosamente',
            'El pago ha sido actualizado correctamente'
        );

        $this->dispatch('refresh-payment-table');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.payment.index.edit');
    }
}
