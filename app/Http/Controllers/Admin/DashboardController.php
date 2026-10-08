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
        return view('admin.dashboard', [
            'menuCount' => MenuItem::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'recentOrders' => Order::withCount('items')->latest()->limit(5)->get(),
        ]);
    }
}
