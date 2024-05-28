<?php

use App\Http\Controllers\CurrentCategoryController;
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

// Route to redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route to download personal account statement
Route::get('/personal-account-statement', App\Livewire\Public\Buyer\Statement\Page::class)->name('personal-account-statement');

// Route to download general parcel report
Route::get('/personal-account-statement/download/{promise}/{buyer}', [App\Http\Controllers\Reports\StatementController::class, 'download'])->name('statement-download');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', App\Livewire\Dashboard\Index\Page::class)->name('dashboard');

    // Buyers
    Route::get('/buyers', App\Livewire\Buyer\Index\Page::class)->name('buyers');
    Route::get('/buyers/{buyer}/edit', App\Livewire\Buyer\Edit\Page::class)->name('buyers.edit');

    // Parcels
    Route::get('/parcels', App\Livewire\Parcel\Index\Page::class)->name('parcels');
    Route::get('/parcels/create', App\Livewire\Parcel\Create\Page::class)->name('parcels.create');
    Route::get('/parcels/{parcel}/edit', App\Livewire\Parcel\Edit\Page::class)->name('parcels.edit');

    // Blocks
    Route::get('/blocks', App\Livewire\Block\Index\Page::class)->name('blocks');

    // Payments
    Route::get('/payments', App\Livewire\Payment\Index\Page::class)->name('payments');
    Route::get('/payments/create', App\Livewire\Payment\Create\Page::class)->name('payments.create');
    Route::get('/payments/{payment}/edit', App\Livewire\Payment\Edit\Page::class)->name('payments.edit');

    // Promises
    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->name('promises');
    Route::get('/promises/create', App\Livewire\Promise\Create\Page::class)->name('promises.create');
    Route::get('/promises/{promise}/edit', App\Livewire\Promise\Edit\Page::class)->name('promises.edit');

    // Categories
    Route::get('/categories', App\Livewire\Category\Index\Page::class)->name('categories');

    // Deeds
    Route::get('/deeds', App\Livewire\Deed\Index\Page::class)->name('deeds');
    Route::get('/deeds/{deed}/edit', App\Livewire\Deed\Edit\Page::class)->name('deeds.edit');

    // Notifications
    Route::get('/notifications', App\Livewire\Notification\Index\Page::class)->name('notifications');

    // Settings
    Route::get('/settings', App\Livewire\Setting\Index\Page::class)->name('settings');

    // Users
    Route::get('/users', App\Livewire\User\Index\Page::class)->name('users');

    // Roles & Permissions
    Route::get('/permissions', App\Livewire\Permission\Index\Page::class)->name('permissions');

    // Current Category
    Route::put('/current-category', [CurrentCategoryController::class, 'update'])->name('current-category.update');

    /*
    |--------------------------------------------------------------------------
    | API Routes for Selects
    |--------------------------------------------------------------------------
    */
    Route::get('/select/buyers', \App\Http\Controllers\Api\Buyers\Index::class)->name('api.buyers.index');
    Route::get('/select/promises', \App\Http\Controllers\Api\Promises\Index::class)->name('api.promises.index');
    Route::get('/select/categories', \App\Http\Controllers\Api\Categories\Index::class)->name('api.categories.index');
    Route::get('/select/blocks', \App\Http\Controllers\Api\Blocks\Index::class)->name('api.blocks.index');
    Route::get('/select/blocks/parcels/promises/{block?}', [\App\Http\Controllers\Api\Parcels\Index::class, 'promises'])->name('api.parcels.promises');
    Route::get('/select/blocks/parcels/deeds/{block?}', [\App\Http\Controllers\Api\Parcels\Index::class, 'deeds'])->name('api.parcels.deeds');
});
