<?php

namespace App\Livewire\Forms;

use App\Enums\ParcelPosition;
use App\Models\Parcel;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ParcelForm extends Form
{
    public ?Parcel $parcel;

    #[Validate('required|string|max:255', 'numero')]
    public $number;

    #[Validate('required', 'ubicacion')]
    public ParcelPosition $position;

    #[Validate('nullable|string', 'localizacion')]
    public $location;

    #[Validate('nullable|string', 'area')]
    public $area;

    #[Validate('nullable|numeric', 'area en m2')]
    public $area_m2 = 0;

    #[Validate('nullable|numeric', 'valor')]
    public $value = 0;

    #[Validate('required|exists:blocks,id', 'manzana')]
    public $block_id;

    #[Validate('nullable|exists:promises,id', 'promesa')]
    public $promise_id;

    public function setParcel(Parcel $parcel)
    {
        $this->parcel = $parcel;
        $this->fill($parcel->only([
            'number',
            'position',
            'location',
            'area',
            'area_m2',
            'value',
            'block_id',
            'promise_id',
        ]));
    }

    public function save()
    {
        $this->validate();

        if (Parcel::where('number', $this->number)->exists()) {
            $this->addError('number', 'Ya existe un lote con este número');
            return;
        }

        Parcel::create([
            'number' => $this->number,
            'position' => $this->position,
            'location' => $this->location,
            'area' => $this->area,
            'area_m2' => $this->area_m2,
            'value' => $this->value,
            'block_id' => $this->block_id,
            'promise_id' => $this->promise_id,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        if (Parcel::where('number', $this->number)->where('id', '!=', $this->parcel->id)->exists()) {
            $this->addError('number', 'Ya existe un lote con este número');
            return;
        }

        $this->parcel->update([
            'number' => $this->number,
            'position' => $this->position,
            'location' => $this->location,
            'area' => $this->area,
            'area_m2' => $this->area_m2,
            'value' => $this->value,
            'block_id' => $this->block_id,
            'promise_id' => $this->promise_id,
        ]);

        $this->reset();
    }
}
