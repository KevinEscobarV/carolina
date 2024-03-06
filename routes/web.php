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
    return redirect()->route('login');
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
    Route::get('/payments/{payment}/edit', App\Livewire\Payment\Edit\Page::class)->name('payments.edit');

    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->name('promises');

    Route::get('/categories', App\Livewire\Category\Index\Page::class)->name('categories');

    Route::get('/deeds', App\Livewire\Deed\Index\Page::class)->name('deeds');
});
