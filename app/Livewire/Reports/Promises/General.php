<?php

namespace App\Livewire\Reports\Promises;

use App\Exports\Promises\General as PromisesGeneral;
use Livewire\Component;

class General extends Component
{
    public $fromDate;
    public $toDate;

    public function mount()
    {
        $this->fromDate = now()->subYears(3);
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

        $name = 'Reporte-Promesas-' . now()->format('Y-m-d-h') . '.xlsx';
        return (new PromisesGeneral($this->fromDate, $this->toDate))->download($name);
    }

    public function render()
    {
        return view('livewire.reports.promises.general');
    }
}
