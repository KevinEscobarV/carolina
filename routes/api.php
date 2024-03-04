<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/buyers', \App\Http\Controllers\Api\Buyers\Index::class)->name('api.buyers.index');
Route::get('/promises', \App\Http\Controllers\Api\Promises\Index::class)->name('api.promises.index');
Route::get('/categories', \App\Http\Controllers\Api\Categories\Index::class)->name('api.categories.index');
Route::get('/blocks', \App\Http\Controllers\Api\Blocks\Index::class)->name('api.blocks.index');
Route::get('/blocks/parcels/promises/{block?}', [\App\Http\Controllers\Api\Parcels\Index::class, 'promises'])->name('api.parcels.promises');
Route::get('/blocks/parcels/deeds/{block?}', [\App\Http\Controllers\Api\Parcels\Index::class, 'deeds'])->name('api.parcels.deeds');
