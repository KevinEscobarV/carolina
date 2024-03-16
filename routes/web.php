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
    Route::get('/dashboard', App\Livewire\Dashboard\Index\Page::class)->name('dashboard');

    Route::get('/buyers', App\Livewire\Buyer\Index\Page::class)->name('buyers');
    Route::get('/buyers/{buyer}/edit', App\Livewire\Buyer\Edit\Page::class)->name('buyers.edit');

    Route::get('/parcels', App\Livewire\Parcel\Index\Page::class)->name('parcels');

    Route::get('/blocks', App\Livewire\Block\Index\Page::class)->name('blocks');

    Route::get('/payments', App\Livewire\Payment\Index\Page::class)->name('payments');
    Route::get('/payments/{payment}/edit', App\Livewire\Payment\Edit\Page::class)->name('payments.edit');

    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->name('promises');
    Route::get('/promises/{promise}/edit', App\Livewire\Promise\Edit\Page::class)->name('promises.edit');

    Route::get('/categories', App\Livewire\Category\Index\Page::class)->name('categories');

    Route::get('/deeds', App\Livewire\Deed\Index\Page::class)->name('deeds');
    Route::get('/deeds/{deed}/edit', App\Livewire\Deed\Edit\Page::class)->name('deeds.edit');
});
