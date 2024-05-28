<?php

namespace App\Livewire\Forms;

use App\Enums\PaymentFrequency;
use App\Enums\PaymentMethod;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Models\Parcel;
use App\Models\Promise;
use Carbon\Carbon;
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

    #[Validate('nullable', 'fecha de firma de escritura')]
    public $signature_deed_date;

    #[Validate('required|numeric', 'valor de la promesa')]
    public $value = 0;

    #[Validate('nullable|numeric', 'cuota inicial')]
    public $initial_fee = 0;

    #[Validate('required|numeric', 'monto de cuota')]
    public $quota_amount = 0;

    #[Validate('required|numeric', 'tasa de interés')]
    public $interest_rate = 0;

    #[Validate('required', 'frecuencia de pago')]
    public $payment_frequency = PaymentFrequency::MONTHLY;

    #[Validate('nullable|date', 'fecha de corte')]
    public $cut_off_date;

    #[Validate('nullable', 'método de pago')]
    public $payment_method = PromisePaymentMethod::CASH;

    #[Validate('nullable', 'estado')]
    public $status = PromiseStatus::CONFIRMED;

    #[Validate('required', 'abono de pago')]
    public $switch_payment = false;

    #[Validate('required', 'proyección de pago')]
    public $switch_quota = false;

    #[Validate('required|numeric|min:1', 'número de cuotas')]
    public $number_of_fees = 1;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    #[Validate('array', 'proyección de pagos')]
    public $projection = [];

    public function setModel(Promise $model): void
    {
        $this->model = $model;
        $this->fill($model->only([
            'number',
            'signature_date',
            'signature_deed_date',
            'value',
            'initial_fee',
            'quota_amount',
            'interest_rate',
            'cut_off_date',
            'payment_frequency',
            'payment_method',
            'status',
            'switch_payment',
            'switch_quota',
            'observations',
            'projection',
            'number_of_fees'
        ]));

        $this->buyers = $model->buyers->pluck('id')->toArray();
        $this->parcels = $model->parcels->pluck('id')->toArray();
    }

    public function amortization()
    {
        $this->validate([
            'quota_amount' => 'required|numeric',
            'number_of_fees' => 'required|numeric|min:1',
            'cut_off_date' => 'required|date',
            'payment_frequency' => 'required',
        ], [], [
            'quota_amount' => 'monto de cuota',
            'number_of_fees' => 'número de cuotas',
            'cut_off_date' => 'fecha de corte',
            'payment_frequency' => 'frecuencia de pago',
        ]);

        $interes = $this->interest_rate;
        $valorTotal = $this->value - $this->initial_fee;
        $numCuotas = $this->number_of_fees;
        $fechaPago = Carbon::parse($this->cut_off_date);
        $periodicidad = $this->payment_frequency;

        $cronograma = [];

        // Calcular la tasa de interés según la periodicidad
        $tasaInteres = $interes > 0
            ? $interes / 100 / $periodicidad->multiplier()
            : 0;

        // Calcular el valor de la cuota
        $valorCuota = $tasaInteres > 0
            ? ($valorTotal * $tasaInteres) / (1 - pow((1 + $tasaInteres), -$numCuotas))
            : $valorTotal / $numCuotas;

        // Calcular el balance inicial
        $balance = $valorTotal;

        // Iterar sobre el número de cuotas
        for ($cuota = 1; $cuota <= $numCuotas; $cuota++) {
            // Calcular el pago de intereses
            $pagoIntereses = $balance * $tasaInteres;

            // Calcular el pago a capital
            $pagoCapital = $valorCuota - $pagoIntereses;

            // Calcular el nuevo balance
            $balance -= $pagoCapital;

            // Agregar detalles del pago al array
            $cronograma[] = [
                'number' => $cuota,
                'due_date' => $fechaPago->format('Y-m-d'),
                'payment_amount' => round($valorCuota, 2),
                'interest_payment' => round($pagoIntereses, 2),
                'principal_payment' => round($pagoCapital, 2),
                'remaining_balance' => round($balance, 2),
            ];

            // Actualizar la fecha de corte
            $fechaPago = match ($this->payment_frequency) {
                PaymentFrequency::WEEKLY => $fechaPago->addWeek(),
                PaymentFrequency::BIWEEKLY => $fechaPago->addWeeks(2),
                PaymentFrequency::MONTHLY => $fechaPago->addMonth(),
                PaymentFrequency::QUARTERLY => $fechaPago->addMonths(3),
                PaymentFrequency::SEMI_ANNUAL => $fechaPago->addMonths(6),
                PaymentFrequency::ANNUAL => $fechaPago->addYear(),
                PaymentFrequency::IRREGULAR => $fechaPago,
            };
        }

        $this->projection = json_decode(json_encode($cronograma), true);

        return true;
    }


    public function save()
    {
        if (!is_array($this->projection) || empty($this->projection)) {
            $this->amortization();
        }

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
