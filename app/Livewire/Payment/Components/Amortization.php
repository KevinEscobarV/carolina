<?php

namespace App\Livewire\Payment\Components;

use App\Models\Promise;
use Livewire\Attributes\On;
use Livewire\Component;

class Amortization extends Component
{
    public $promiseId;

    public $promise;
    public $projection = [];

    public function deleteQuota($index)
    {
        unset($this->projection[$index]);
    }

    public function addQuota()
    {
        $this->projection[] = [
            'number' => count($this->projection) + 1,
            'due_date' => now()->format('Y-m-d'),
            'payment_amount' => 0,
            'interest_payment' => 0,
            'principal_payment' => 0,
            'remaining_balance' => 0,
        ];
    }

    public function addPayments()
    {
        $payments = $this->promise->payments->where('is_initial_fee', false)->sortBy('payment_date');
        $check = $payments->count() + 1;
        $number = 1;

        foreach ($this->projection as $key => $quota) {
            if ($check == $number) {
                $this->projection[$key]['is_last'] = true;
            }

            $this->projection[$key]['number'] = $number++;
            $this->projection[$key]['payment'] = null;
        }

        $payments->each(function ($payment) {
            $payment->payment_date_formatted = $payment->payment_date ? $payment->payment_date->translatedFormat("F j/Y") : 'Sin definir';
            $payment->paid_amount_formatted = number_format($payment->paid_amount, 0, ',', '.');
            foreach ($this->projection as $key => $quota) {
                if (!isset($this->projection[$key]['payment'])) {
                    $this->projection[$key]['payment'] = [
                        'id' => $payment->id,
                        'bill_number' => $payment->bill_number,
                        'payment_date_formatted' => $payment->payment_date_formatted,
                        'paid_amount_formatted' => $payment->paid_amount_formatted,
                    ];
                    break;
                }
            }
        });
    }

    #[On('show-amortization')]
    public function setProjection(Promise $promise)
    {
        $this->promise = $promise;
        $this->projection = $promise->projection;
        $this->addPayments();
    }

    public function render()
    {
        return view('livewire.payment.components.amortization');
    }
}
