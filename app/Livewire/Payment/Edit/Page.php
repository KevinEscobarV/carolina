<?php

namespace App\Livewire\Payment\Edit;

use App\Livewire\Forms\PaymentForm;
use App\Models\Payment;
use Livewire\Component;
use WireUi\Traits\Actions;

class Page extends Component
{
    use Actions;
    
    public Payment $payment;

    public PaymentForm $form;

    public function mount()
    {
        $this->form->setPayment($this->payment);
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
