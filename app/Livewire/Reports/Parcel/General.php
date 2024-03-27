<?php

namespace App\Livewire\Reports\Parcel;

use App\Exports\Parcel\General as ParcelGeneral;
use Livewire\Component;
use WireUi\Traits\Actions;

class General extends Component
{
    use Actions;
    public $status = '';
    public $position = '';
    public $registrationNumber = '';

    public function exportGeneral()
    {
        try {
            $name = 'Reporte-Lotes-' . now()->format('Y-m-d-h') . '.xlsx';
            $report = new ParcelGeneral($this->position, $this->status, $this->registrationNumber);

            $this->notification()->success('Reporte generado', 'El reporte se ha exportado correctamente');

            return $report->download($name);

        } catch (\Throwable $th) {
            $this->notification()->error(
                'Error al exportar el reporte',
                'Ocurrió un error al exportar el reporte, por favor intente nuevamente, error: ' . $th->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.reports.parcel.general');
    }
}
