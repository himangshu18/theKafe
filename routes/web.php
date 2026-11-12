<?php

use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ComboController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuCustomizationController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TodaySpecialController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/confirmation/{order:public_ref}', [OrderController::class, 'confirmation'])->name('order.confirmation');

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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::middleware('admin-role:owner,manager')->group(function () {
        Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
        Route::post('/menu', [MenuItemController::class, 'store'])->name('menu.store');
        Route::get('/menu/{menuItem}/customize', [MenuCustomizationController::class, 'edit'])->name('menu.customize.edit');
        Route::put('/menu/{menuItem}/customize', [MenuCustomizationController::class, 'update'])->name('menu.customize.update');
        Route::put('/menu/{menuItem}', [MenuItemController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu.destroy');

        Route::get('/categories', [MenuCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [MenuCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [MenuCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [MenuCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/combos', [ComboController::class, 'index'])->name('combos.index');
        Route::get('/combos/create', [ComboController::class, 'create'])->name('combos.create');
        Route::post('/combos', [ComboController::class, 'store'])->name('combos.store');
        Route::get('/combos/{combo}/edit', [ComboController::class, 'edit'])->name('combos.edit');
        Route::put('/combos/{combo}', [ComboController::class, 'update'])->name('combos.update');
        Route::delete('/combos/{combo}', [ComboController::class, 'destroy'])->name('combos.destroy');

        Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
        Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
        Route::get('/offers/{offer}/edit', [OfferController::class, 'edit'])->name('offers.edit');
        Route::put('/offers/{offer}', [OfferController::class, 'update'])->name('offers.update');
        Route::delete('/offers/{offer}', [OfferController::class, 'destroy'])->name('offers.destroy');

        Route::get('/specials', [TodaySpecialController::class, 'index'])->name('specials.index');
        Route::get('/specials/create', [TodaySpecialController::class, 'create'])->name('specials.create');
        Route::post('/specials', [TodaySpecialController::class, 'store'])->name('specials.store');
        Route::get('/specials/{todaySpecial}/edit', [TodaySpecialController::class, 'edit'])->name('specials.edit');
        Route::put('/specials/{todaySpecial}', [TodaySpecialController::class, 'update'])->name('specials.update');
        Route::delete('/specials/{todaySpecial}', [TodaySpecialController::class, 'destroy'])->name('specials.destroy');
    });

    Route::middleware('admin-role:owner')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::patch('/staff/{user}', [StaffController::class, 'update'])->name('staff.update');
    });

    Route::middleware('admin-role:owner,manager,cashier')->group(function () {
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    });

    Route::middleware('admin-role:owner,manager,cashier,kitchen')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    });

    Route::middleware('admin-role:owner,manager,cashier')->post('/orders/{order}/payments', [AccountingController::class, 'storePayment'])->name('orders.payments.store');

    Route::middleware('admin-role:owner,manager,accountant')->prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('index');
        Route::get('/export', [AccountingController::class, 'export'])->name('export');
        Route::post('/expenses', [AccountingController::class, 'storeExpense'])->name('expenses.store');
        Route::post('/expense-categories', [AccountingController::class, 'storeExpenseCategory'])->name('categories.store');
        Route::post('/cash-sessions', [AccountingController::class, 'openCashSession'])->name('cash-sessions.store');
        Route::patch('/cash-sessions/{cashSession}/close', [AccountingController::class, 'closeCashSession'])->name('cash-sessions.close');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
