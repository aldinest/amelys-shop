<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('welcome');
    });

    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        //Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/create', [AdminDashboardController::class, 'create'])->name('create');
        Route::post('/store', [AdminDashboardController::class, 'store'])->name('store');

        Route::get('/edit/{user}', [AdminDashboardController::class, 'edit'])->name('edit');
        Route::put('/update/{user}', [AdminDashboardController::class, 'update'])->name('update');

        Route::delete('/delete/{user}', [AdminDashboardController::class, 'destroy'])->name('destroy');

    });


    Route::middleware(['auth', 'role:user'])
        ->prefix('user')
        ->name('user.')
        ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

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


    //Atur Arah login sesuai role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isUser()) {
            return redirect()->route('user.dashboard');
        }

        abort(403);
    })->middleware(['auth'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
