<?php

use App\Livewire\Buyers\BuyerComponent;
use App\Livewire\Parcels\ParcelComponent;
use App\Livewire\Payments\PaymentComponent;
use App\Models\Payment;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/buyers', BuyerComponent::class)->name('buyers');

    Route::get('/parcels', ParcelComponent::class)->name('parcels');

    Route::get('/payments', PaymentComponent::class)->name('payments');
});
