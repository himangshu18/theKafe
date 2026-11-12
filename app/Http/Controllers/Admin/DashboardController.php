<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MenuItem;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();

        return view('admin.dashboard', [
            'menuCount' => $user->canManageMenu() ? MenuItem::count() : null,
            'pendingBookings' => $user->canManageBookings() ? Booking::where('status', 'pending')->count() : null,
            'pendingOrders' => $user->canManageOrders()
                ? Order::whereIn('status', ['submitted', 'accepted', 'preparing', 'ready'])->count()
                : null,
            'recentOrders' => $user->canManageOrders()
                ? Order::withCount('items')->latest()->limit(5)->get()
                : collect(),
        ]);
    }
}
