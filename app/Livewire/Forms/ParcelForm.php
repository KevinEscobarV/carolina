<?php

namespace App\Livewire\Forms;

use App\Enums\ParcelPosition;
use App\Models\Parcel;
use Livewire\Attributes\Validate;
use Livewire\Form;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;

class ParcelForm extends Form
{
    public ?Parcel $parcel;

    #[Validate('required', 'numero')]
    public $number;

    #[Validate('required', 'ubicacion')]
    public ParcelPosition $position = ParcelPosition::POSITION_MIDDLE;

    #[Validate('nullable', 'localizacion')]
    public $location;

    #[Validate('nullable', 'area')]
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
            'area_m2',
            'value',
            'block_id',
            'promise_id',
        ]));
    }

    public function save()
    {
        $this->validate();

        if (Parcel::where('number', $this->number)->where('block_id', $this->block_id)->exists()) {
            $this->addError('number', 'Ya existe un lote con este número en esta manzana');
            return;
        }

        Parcel::create([
            'number' => $this->number,
            'position' => $this->position,
            'location' => $this->location ? new Point($this->location['lat'], $this->location['lng']) : null,
            'area' => $this->area ? new Polygon([new LineString(array_map(fn ($point) => new Point($point['lat'], $point['lng']), $this->area))]) : null,
            'area_m2' => $this->area_m2,
            'value' => $this->value,
            'block_id' => $this->block_id,
            'promise_id' => $this->promise_id,
        ]);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        if (Parcel::where('number', $this->number)->where('block_id', $this->block_id)->where('id', '!=', $this->parcel->id)->exists()) {
            $this->addError('number', 'Ya existe un lote con este número en esta manzana');
            return;
        }

        $this->parcel->update([
            'number' => $this->number,
            'position' => $this->position,
            'location' => $this->location ? new Point($this->location['lat'], $this->location['lng']) : $this->parcel->location,
            'area' => $this->area ? new Polygon([new LineString(array_map(fn ($point) => new Point($point['lat'], $point['lng']), $this->area))]) : $this->parcel->area,
            'area_m2' => $this->area_m2,
            'value' => $this->value,
            'block_id' => $this->block_id,
            'promise_id' => $this->promise_id,
        ]);

        $this->reset();

        return true;
    }
}
