<?php

namespace App\Livewire\Public\Buyer\Statement;

use App\Models\Buyer;
use App\Models\Promise;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;
use Barryvdh\DomPDF\Facade\Pdf;

class Page extends Component
{
    use Actions;


    #[Url('document')]
    public $document_number = '';

    public $buyer;
    public $promises = [];

    public function explore()
    {
        $buyer = Buyer::where('document_number', $this->document_number)->with('promises')->first();

        if (!$buyer) {
            $this->dialog()->error(
                'Ups !!!',
                'No se encontró ningún comprador con el número de documento ingresado.'
            );
            $this->promises = [];
            return;
        }

        $this->buyer = $buyer;
        $this->promises = $buyer->promises;
    }

    public function download(Promise $promise)
    {
        $pdf = Pdf::loadView('exports.statement', ['promise' => $promise, 'buyer' => $this->buyer])->setPaper('a4', 'landscape')->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        // return $pdf->download('estado-cuenta.pdf');

        return $pdf->stream();

        // return response()->streamDownload(function () use ($pdf) {
        //     echo $pdf->stream();
        //     }, 'estado-cuenta.pdf');
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.public.buyer.statement.page');
    }
}
