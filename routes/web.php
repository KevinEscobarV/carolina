<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/test', function () {
    return "Hello World";
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
});
