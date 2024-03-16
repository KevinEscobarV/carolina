<?php

namespace App\Livewire\Reports\Payment;

use App\Exports\Payment\General as PaymentGeneral;
use Livewire\Component;

class General extends Component
{
    public $fromDate;
    public $toDate;

    public function mount()
    {
        $this->fromDate = now()->subMonth();
        $this->toDate = now();
    }

    public function exportGeneral()
    {
        $name = 'Reporte-Pagos-' . now()->format('Y-m-d-h') . '.xlsx';
        return (new PaymentGeneral($this->fromDate, $this->toDate))->download($name);
    }

    public function render()
    {
        return view('livewire.reports.payment.general');
    }
}
