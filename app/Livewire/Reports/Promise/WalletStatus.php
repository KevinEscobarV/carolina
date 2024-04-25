<?php

namespace App\Livewire\Reports\Promise;

use App\Exports\Promise\WalletStatus as PromiseWalletStatus;
use App\Exports\WalletStatus as ExportsWalletStatus;
use Livewire\Component;
use WireUi\Traits\Actions;

class WalletStatus extends Component
{    
    use Actions;
    public $fromDate;
    public $toDate;
    public $onlyLate = false;

    public function exportGeneral()
    {
        $this->validate([
            'fromDate' => 'nullable|date',
            'toDate' => 'nullable|date',
            'onlyLate' => 'nullable|boolean'
        ],
        [],
        [
            'fromDate' => 'fecha de inicial',
            'toDate' => 'fecha de final',
            'onlyLate' => 'solo promesas en mora'
        ]);

        try {
            $name = 'Reporte-Estado-Cartera-' . now()->format('Y-m-d-h') . '.xlsx';
            $report = new PromiseWalletStatus($this->fromDate, $this->toDate, $this->onlyLate);

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
        return view('livewire.reports.promise.wallet-status');
    }
}
