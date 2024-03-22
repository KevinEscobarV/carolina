<?php

namespace App\Livewire\Parcel\Edit;

use App\Livewire\Forms\ParcelForm;
use App\Models\Parcel;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Editar lote')]
class Page extends Component
{
    use Actions;
    public ParcelForm $form;

    public Parcel $parcel;

    public $marker;
    public $polygon;

    public function mount()
    {
        $this->form->setParcel($this->parcel);
        
        $this->marker = $this->parcel->location ? $this->parcel->location->toJson() : null;

        $this->polygon = $this->parcel->area ? $this->parcel->area->toJson() : null;
    }

    public function save(): void
    {
        $save = $this->form->update();

        if ($save) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Lote actualizado exitosamente',
                'description' => 'El lote se ha actualizado correctamente',
                'accept'      => [
                    'label' => 'Volver a tabla de lotes',
                    'url' => route('parcels'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function setCoordinates($shape, $coordinates): void
    {
        if ($shape === 'Marker') {
            $this->form->location = $coordinates;
            $this->notification()->success(
                'Ubicación asignada',
            );
            return;
        }

        if ($shape === 'Polygon') {
            // Añadir el primer punto al final del array para cerrar el polígono
            $this->form->area = array_merge($coordinates[0], [$coordinates[0][0]]);
            $this->notification()->success(
                'Área asignada',
            );
            return;
        }
    }
    public function render()
    {
        return view('livewire.parcel.edit.page');
    }
}
