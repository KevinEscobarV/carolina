<?php

namespace App\Livewire\Payment\Index;

use App\Livewire\Forms\PaymentForm;
use Livewire\Component;

class Create extends Component
{
    public PaymentForm $form;

    public function mount()
    {
        $this->form->payment_date = now();
    }

    public function save(): void
    {
        $this->form->save();
        $this->dispatch('refresh-payment-table');
    }

    public function render()
    {
        return view('livewire.payment.index.create');
    }
}
