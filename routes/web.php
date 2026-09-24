<?php

use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

// Guests land on the login screen first.
Route::get('/', fn () => redirect()->route('login'))->middleware('guest');
Route::get('/PKMStore', fn () => redirect()->route('login'))->middleware('guest')->name('pkmstore');

// Authenticated storefront.
Route::get('/shop', [SystemController::class, 'index'])->middleware('auth')->name('shop');

// Student Routes (Requires Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [SystemController::class, 'viewCart'])->name('cart');
    Route::post('/cart/add/{id}', [SystemController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/{id}', [SystemController::class, 'updateCart'])->name('cart.update');
    Route::post('/checkout', [SystemController::class, 'checkout'])->name('checkout');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [SystemController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::post('/products', [SystemController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [SystemController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{product}', [SystemController::class, 'updateProduct'])->name('admin.products.update');
    Route::post('/products/{product}/stock', [SystemController::class, 'updateStock'])->name('admin.products.stock');
    Route::delete('/products/{product}', [SystemController::class, 'destroyProduct'])->name('admin.products.delete');
});

require __DIR__ . '/auth.php';