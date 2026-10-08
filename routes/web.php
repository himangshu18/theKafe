<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

Route::get('/book-party', [BookingController::class, 'create'])->name('booking.create');
Route::post('/book-party', [BookingController::class, 'store'])->name('booking.store');

Route::get('/dashboard', function () {
    if (auth()->user()?->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
    Route::post('/menu', [MenuItemController::class, 'store'])->name('menu.store');
    Route::put('/menu/{menuItem}', [MenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu.destroy');

    Route::get('/categories', [MenuCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [MenuCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [MenuCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [MenuCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
