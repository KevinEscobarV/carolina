<?php

use App\Imports\DataImport;
use App\Models\Buyer;
use App\Models\Parcel;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    $collection = Excel::toCollection(new DataImport, public_path('imports/transacciones.xlsx'));

    $users = $collection->first()->map(function ($row) {

        if ($row['mz'] && $row['lote']) {
            $parcel = Parcel::where('number', $row['lote'])->with('promise')->whereHas('block', function ($query) use ($row) {
                $query->where('code', $row['mz']);
            })->first();

            if ($parcel) {
                return [
                    'parcel' => $parcel->id,
                    'promesa' => $parcel->promise ? $parcel->promise->id : '🟡 No tiene promesa',
                    'numero' => $row['recibo_no'],
                    'mz' => $row['mz'],
                    'lote' => $row['lote'],
                    'banco' => $row['banco'],
                ];
            } else {
                return [
                    'parcel' => '🔵 No se encontró el lote',
                    'promesa' => 'No se encontró el lote',
                    'numero' => $row['recibo_no'],
                    'mz' => $row['mz'],
                    'lote' => $row['lote'],
                ];
            }

        } else {

            return [
                'parcel' => '🔴 No tiene lote',
                'promesa' => '🔴 No tiene promesa',
            ];

        }
    });

    return $users;
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/buyers', App\Livewire\Buyer\Index\Page::class)->name('buyers');

    Route::get('/parcels', App\Livewire\Parcel\Index\Page::class)->name('parcels');

    Route::get('/blocks', App\Livewire\Block\Index\Page::class)->name('blocks');

    Route::get('/payments', App\Livewire\Payment\Index\Page::class)->name('payments');

    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->name('promises');

    Route::get('/categories', App\Livewire\Category\Index\Page::class)->name('categories');

    Route::get('/deeds', App\Livewire\Deed\Index\Page::class)->name('deeds');
});
