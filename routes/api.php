<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

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
    Route::get('/', [CartController::class, 'show'])->name('cart.show');
    Route::post('/', [CartController::class, 'addItem'])->name('cart.add');
    Route::delete('/{itemId}', [CartController::class, 'removeItem'])->name('cart.remove');
});