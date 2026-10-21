<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Promise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatementController extends Controller
{
    public function download(Request $request, Promise $promise, Buyer $buyer)
    {
        // Solo se aceptan los enlaces firmados que genera la consulta pública
        if (! $request->hasValidRelativeSignature()) {
            return redirect()->route('personal-account-statement')->dangerBanner('El enlace no es válido o ya venció. Busca de nuevo tu número de documento.');
        }

        // Si el usuario no es el dueño del promise, no puede descargar el reporte
        if ($promise->buyers->contains('id', $buyer->id) === false) {
            return redirect()->route('personal-account-statement')->dangerBanner('No puedes descargar el reporte de una promesa que no te pertenece.');
        }

        $total = $promise->payments()->select( DB::raw('SUM(agreement_amount) as total_agreement_amount'), DB::raw('SUM(paid_amount) as total_paid_amount') )->first();
        $pdf = Pdf::loadView('exports.statement', [
            'promise' => $promise, 
            'buyer' => $buyer ,
            'payments' => $promise->payments,
            'total_agreement_amount' => number_format($total->total_agreement_amount, 0, ',', '.'),
            'total_paid_amount' => number_format($total->total_paid_amount, 0, ',', '.'),
        ]);

        return $pdf->stream();
    }
}
