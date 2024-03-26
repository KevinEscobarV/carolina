<?php

namespace App\Livewire\Reports\Buyer;

use App\Exports\Buyer\General as BuyerGeneral;
use Livewire\Component;
use WireUi\Traits\Actions;

class General extends Component
{
    use Actions;
    public $fromDate;
    public $toDate;

    public function mount()
    {
        $this->fromDate = now()->subYear();
        $this->toDate = now();
    }

    public function exportGeneral()
    {
        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
        ],
        [],
        [
            'fromDate' => 'fecha de inicial',
            'toDate' => 'fecha de final',
        ]);

        try {
            $name = 'Reporte-Clientes-' . now()->format('Y-m-d-h') . '.xlsx';
            $report = new BuyerGeneral($this->fromDate, $this->toDate);

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
        return view('livewire.reports.buyer.general');
    }
}
