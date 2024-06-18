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
    Route::get('/buyers', App\Livewire\Buyer\Index\Page::class)->middleware('can:view.buyers')->name('buyers');
    Route::get('/buyers/{buyer}/edit', App\Livewire\Buyer\Edit\Page::class)->middleware('can:edit.buyers')->name('buyers.edit');

    // Parcels
    Route::get('/parcels', App\Livewire\Parcel\Index\Page::class)->middleware('can:view.parcels')->name('parcels');
    Route::get('/parcels/create', App\Livewire\Parcel\Create\Page::class)->middleware('can:create.parcels')->name('parcels.create');
    Route::get('/parcels/{parcel}/edit', App\Livewire\Parcel\Edit\Page::class)->middleware('can:edit.parcels')->name('parcels.edit');

    // Blocks
    Route::get('/blocks', App\Livewire\Block\Index\Page::class)->middleware('can:view.blocks')->name('blocks');

    // Payments
    Route::get('/payments', App\Livewire\Payment\Index\Page::class)->middleware('can:view.payments')->name('payments');
    Route::get('/payments/create', App\Livewire\Payment\Create\Page::class)->middleware('can:create.payments')->name('payments.create');
    Route::get('/payments/{payment}/edit', App\Livewire\Payment\Edit\Page::class)->middleware('can:edit.payments')->name('payments.edit');

    // Promises
    Route::get('/promises', App\Livewire\Promise\Index\Page::class)->middleware('can:view.promises')->name('promises');
    Route::get('/promises/create', App\Livewire\Promise\Create\Page::class)->middleware('can:create.promises')->name('promises.create');
    Route::get('/promises/{promise}/edit', App\Livewire\Promise\Edit\Page::class)->middleware('can:edit.promises')->name('promises.edit');

    // Categories
    Route::get('/categories', App\Livewire\Category\Index\Page::class)->middleware('can:view.categories')->name('categories');

    // Deeds
    Route::get('/deeds', App\Livewire\Deed\Index\Page::class)->middleware('can:view.deeds')->name('deeds');
    Route::get('/deeds/{deed}/edit', App\Livewire\Deed\Edit\Page::class)->middleware('can:edit.deeds')->name('deeds.edit');

    // Notifications
    Route::get('/notifications', App\Livewire\Notification\Index\Page::class)->middleware('can:send.messages')->name('notifications');

    // Settings
    Route::get('/settings', App\Livewire\Setting\Index\Page::class)->middleware('can:edit.settings')->name('settings');

    // Users
    Route::get('/users', App\Livewire\User\Index\Page::class)->middleware('can:view.users')->name('users');

    // Roles & Permissions
    Route::get('/permissions', App\Livewire\Permission\Index\Page::class)->middleware('can:view.roles')->name('permissions');

    // Current Category
    Route::put('/current-category', [CurrentCategoryController::class, 'update'])->middleware('can:change.category')->name('current-category.update');

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
