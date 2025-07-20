<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

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

Route::get('/', [ProductController::class, 'index']);

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/{itemKey}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clearCart', [CartController::class, 'clearCart'])->name('cart.clearCart');
    Route::patch('/update/{itemKey}', [CartController::class, 'updateCartItem'])->name('cart.updateCartItem');
    Route::post('calculateShipping', [CartController::class, 'calculateShipping'])->name('cart.calculateShipping');
});