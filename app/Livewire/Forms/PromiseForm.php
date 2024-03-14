<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentFrequency;
use App\Enums\PaymentMethod;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Models\Parcel;
use App\Models\Promise;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PromiseForm extends Form
{
    public ?Promise $model;

    #[Validate('required|array|min:1|exists:buyers,id', 'compradores')]
    public $buyers = [];

    #[Validate('required|array|min:1|exists:parcels,id', 'lotes')]
    public $parcels = [];

    #[Validate('required', 'número o codigo de promesa')]
    public $number;

    #[Validate('required', 'fecha de firma')]
    public $signature_date;

    #[Validate('required|numeric', 'valor de la promesa')]
    public $value = 0;

    #[Validate('nullable|numeric', 'cuota inicial')]
    public $initial_fee = 0;

    #[Validate('required|numeric', 'monto de cuota')]
    public $quota_amount = 0;

    #[Validate('required|numeric', 'tasa de interés')]
    public $interest_rate = 0;
    
    #[Validate('required', 'frecuencia de pago')]
    public PaymentFrequency $payment_frequency = PaymentFrequency::MONTHLY;

    #[Validate('nullable|date', 'fecha de corte')]
    public $cut_off_date;

    #[Validate('nullable', 'método de pago')]
    public PromisePaymentMethod $payment_method = PromisePaymentMethod::CASH;

    #[Validate('nullable', 'estado')]
    public PromiseStatus $status = PromiseStatus::CONFIRMED;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    public function setModel(Promise $model): void
    {
        $this->model = $model;
        $this->fill($model->only([
            'number',
            'signature_date',
            'value',
            'initial_fee',
            'quota_amount',
            'interest_rate',
            'payment_frequency',
            'cut_off_date',
            'payment_method',
            'status',
            'observations',
        ]));

        $this->buyers = $model->buyers->pluck('id')->toArray();
        $this->parcels = $model->parcels->pluck('id')->toArray();
    }

    public function save()
    {
        $this->validate();

        $promise = Promise::create($this->all());
        
        $promise->buyers()->attach($this->buyers);

        Parcel::whereIn('id', $this->parcels)->whereNull('promise_id')->update(['promise_id' => $promise->id]);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        $this->model->update($this->all());

        $this->model->buyers()->sync($this->buyers);

        Parcel::where('promise_id', $this->model->id)->update(['promise_id' => null]);

        Parcel::whereIn('id', $this->parcels)->whereNull('promise_id')->update(['promise_id' => $this->model->id]);

        return true;
    }
}
