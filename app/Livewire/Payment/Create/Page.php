<?php

namespace App\Livewire\Payment\Create;

use App\Livewire\Forms\PaymentForm;
use App\Models\Promise;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Crear pago')]
class Page extends Component
{
    use Actions;

    public PaymentForm $form;

    public function autocompleteFields($promise_id): void
    {
        $promise = Promise::with('payments')->find($promise_id);

        if ($this->form->is_initial_fee) {

            // Check if the promise has a initial fee payment
            $initial_fee_payment = $promise->payments->where('is_initial_fee', true)->first();

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

            $number = 1;
            $check = $promise->payments->where('is_initial_fee', false)->count() + 1;

            if ($promise->projection) {
                foreach ($promise->projection as $quota) {
                    if ($check == $number) {
                        $this->form->agreement_date = $quota['due_date'];
                        $this->form->agreement_amount = $quota['payment_amount'];
                        $this->form->paid_amount = $quota['payment_amount'];
                        break;
                    }
                    $number++;
                }
            } else {
                $this->form->agreement_date = now()->format('Y-m-d');
            }
        }

        $this->notification()->info(
            'Se han autocompletado los campos',
            'Recuerda revisar los datos antes de guardar el pago'
        );

        $this->dispatch('show-amortization', $promise);
    }

    public function mount()
    {
        $this->form->payment_date = now()->format('Y-m-d');
        $this->form->agreement_date = now()->format('Y-m-d');
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
        if ($this->form->promise_id) {
            $this->autocompleteFields($this->form->promise_id);
        }

        return view('livewire.payment.create.page');
    }
}
