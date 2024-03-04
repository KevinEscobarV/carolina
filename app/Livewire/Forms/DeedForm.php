<?php

namespace App\Livewire\Forms;

use App\Enums\DeedStatus;
use App\Models\Deed;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeedForm extends Form
{
    public ?Deed $model;

    #[Validate('required|string|max:255', 'número de escritura')]
    public $number;

    #[Validate('required|numeric', 'valor de escritura')]
    public $value;

    #[Validate('nullable|date', 'fecha de firma')]
    public $signature_date;

    #[Validate('nullable', 'libro')]
    public $book;

    #[Validate('nullable', 'estado')]
    public DeedStatus $status;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    #[Validate('required|exists:parcels,id', 'lote')]
    public $parcel_id;

    public function setModel(Deed $model): void
    {
        $this->model = $model;
        $this->fill($model->only([
            'number',
            'value',
            'signature_date',
            'book',
            'status',
            'observations',
            'parcel_id'
        ]));
    }

    public function save(): void
    {
        $this->validate();

        if (Deed::where('number', $this->number)->exists()) {
            $this->addError('number', 'Ya existe una escritura con este número');
            return;
        }

        Deed::create($this->all());

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        if (Deed::where('number', $this->number)->where('id', '!=', $this->model->id)->exists()) {
            $this->addError('number', 'Ya existe una escritura con este número');
            return;
        }

        $this->model->update($this->all());

        $this->reset();
    }
}
