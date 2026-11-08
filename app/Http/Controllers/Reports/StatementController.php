<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reports\AccountStatement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StatementController extends Controller
{
    /**
     * Account statement in PDF, only through the signed links of the public search.
     */
    public function download(Request $request, int $promise, int $buyer): Response|RedirectResponse
    {
        if (! $request->hasValidRelativeSignature()) {
            return redirect()->route('personal-account-statement')->dangerBanner('El enlace no es válido o ya venció. Busca de nuevo tu número de documento.');
        }

        $statement = AccountStatement::find($promise, $buyer);

        if ($statement === null) {
            return redirect()->route('personal-account-statement')->dangerBanner('No puedes descargar el reporte de una promesa que no te pertenece.');
        }

        return $statement->pdf()->stream('Estado-de-cuenta-'.Str::slug((string) $statement->promise->number).'.pdf');
    }
}
