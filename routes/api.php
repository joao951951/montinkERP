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
    Route::get('/getCartItems', [ApiCartController::class, 'getCartItems'])->name('cart.getCartItems');
    Route::post('/store', [ApiCartController::class, 'store'])->name('cart.store');
    Route::patch('/updateCartItem/{itemKey}', [ApiCartController::class, 'update'])->name('cart.updateCartItem');
    Route::delete('/removeItem/{itemKey}', [ApiCartController::class, 'destroy'])->name('cart.removeItem');
});