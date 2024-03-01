<?php

use App\Enums\ParcelPosition;
use App\Imports\DataImport;
use App\Models\Block;
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

    Route::get('/payments', App\Livewire\Payment\Index\Page::class)->name('payments');

    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->name('promises');

    Route::get('/categories', App\Livewire\Category\Index\Page::class)->name('categories');
});
