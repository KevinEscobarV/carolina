<?php

namespace App\Livewire\Reports\Payment;

use App\Exports\Payment\General as PaymentGeneral;
use Livewire\Component;

class General extends Component
{
    public function exportGeneral()
    {
        $fromDate = now()->subYear()->format('Y-m-d');
        $toDate = now()->format('Y-m-d');
        return (new PaymentGeneral($fromDate, $toDate))->download('general.xlsx');
    }

    public function render()
    {
        return view('livewire.reports.payment.general');
    }
}
