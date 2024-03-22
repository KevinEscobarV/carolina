<?php

namespace App\Livewire\Parcel\Create;

use App\Livewire\Forms\ParcelForm;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Crear lote')]
class Page extends Component
{
    use Actions;
    public ParcelForm $form;

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

    public function save(): void
    {
        $save = $this->form->save();

        if ($save) {
            $this->notification()->confirm([
                'icon'        => 'success',
                'title'       => 'Lote creado exitosamente',
                'description' => 'El lote se ha creado correctamente',
                'accept'      => [
                    'label' => 'Volver a tabla de lotes',
                    'url' => route('parcels'),
                ],
                'rejectLabel' => 'Permanecer aquí',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.parcel.create.page');
    }
}
