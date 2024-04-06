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
        $promise = Promise::find($value);
        $this->form->agreement_date = $promise->current_cut_off_date;
        $this->form->agreement_amount = $promise->quota_amount;
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
