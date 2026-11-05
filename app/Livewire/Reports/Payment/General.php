<?php

namespace App\Livewire\Reports\Payment;

use App\Exports\Payment\General as PaymentGeneral;
use Livewire\Component;
use WireUi\Traits\Actions;

class General extends Component
{
    use Actions;

    public $fromDate;

    public $toDate;

    public $paymentMethods = [];

    public function mount()
    {
        $this->fromDate = now()->subMonth();
        $this->toDate = now();
    }

    public function exportGeneral()
    {
        $this->authorize('view.payments');

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
            $name = 'Reporte-Pagos-'.now()->format('Y-m-d-h').'.xlsx';
            $report = new PaymentGeneral($this->fromDate, $this->toDate, $this->paymentMethods);

            $this->notification()->success('Reporte generado', 'El reporte se ha exportado correctamente');

            return $report->download($name);

        } catch (\Throwable $th) {
            $this->notification()->error(
                'Error al exportar el reporte',
                'Ocurrió un error al exportar el reporte, por favor intente nuevamente, error: '.$th->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.reports.payment.general');
    }
}
