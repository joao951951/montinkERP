<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiCartController;

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

Route::prefix('cart')->group(function () {
    Route::get('/getCart', [ApiCartController::class, 'index'])->name('cart.getCart');
    Route::post('/items', [ApiCartController::class, 'store'])->name('cart.store');
    Route::put('/items/{itemKey}', [ApiCartController::class, 'update'])->name('cart.update');
    Route::delete('/items/{itemKey}', [ApiCartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/clear', [ApiCartController::class, 'clear'])->name('cart.clear');
    Route::delete('/removeItem/{itemKey}', [ApiCartController::class, 'removeItem'])->name('cart.remove');
});