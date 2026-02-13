<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {

    //Route Pesanan
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/export/excel', [OrderController::class, 'exportExcel'])
    ->name('orders.export.excel');
    Route::get('/orders/export/pdf', [OrderController::class, 'exportPdf'])
    ->name('orders.export.pdf');

    //Route Produk
    Route::resource('products', ProductController::class)
        ->except(['show']);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
