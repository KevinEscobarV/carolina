<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentFrequency;
use App\Enums\PaymentMethod;
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

    #[Validate('nullable|numeric', 'valor de la promesa')]
    public $value;

    #[Validate('nullable|numeric', 'cuota inicial')]
    public $initial_fee;

    #[Validate('nullable|numeric', 'número de cuotas')]
    public $number_of_fees;

    #[Validate('nullable|numeric', 'tasa de interés')]
    public $interest_rate;
    
    #[Validate('required', 'frecuencia de pago')]
    public PaymentFrequency $payment_frequency;

    #[Validate('nullable|date', 'fecha de corte')]
    public $cut_off_date;

    #[Validate('nullable', 'método de pago')]
    public PaymentMethod $payment_method;

    #[Validate('nullable', 'estado')]
    public PromiseStatus $status;

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
            'number_of_fees',
            'interest_rate',
            'payment_method',
            'observations',
            'status',
        ]));

        $this->buyers = $model->buyers->pluck('id')->toArray();
        $this->parcels = $model->parcels->pluck('id')->toArray();
    }

    public function save(): void
    {
        $this->validate();

        $promise = Promise::create($this->all());
        
        $promise->buyers()->attach($this->buyers);

        Parcel::whereIn('id', $this->parcels)->whereNull('promise_id')->update(['promise_id' => $promise->id]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->model->update($this->all());

        $this->reset();
    }
}