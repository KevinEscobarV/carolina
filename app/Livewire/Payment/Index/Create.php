<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Forms\PaymentForm;
use Livewire\Component;
use WireUi\Traits\Actions;

class Create extends Component
{
    use Actions;

    public PaymentForm $form;

    public function mount()
    {
        $this->form->payment_date = now();
        $this->form->agreement_date = now();
    }

    public function save(): void
    {
        $this->form->save();

        $this->notification()->success(
            'Pago creado exitosamente',
            'El pago ha sido registrado correctamente'
        );
        
        $this->dispatch('refresh-payment-table');
    }

    public function render()
    {
        return view('livewire.payment.index.create');
    }
}
