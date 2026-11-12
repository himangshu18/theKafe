<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request, OrderStatusService $statuses): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:submitted,accepted,preparing,ready,completed,cancelled'],
        ]);

        $orders = Order::query()
            ->with(['items', 'statusHistory'])
            ->when(
                $validated['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $orders->getCollection()->each(
            fn (Order $order) => $order->setAttribute('next_statuses', $statuses->availableTransitions($order))
        );

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order, OrderStatusService $statuses): View
    {
        $order->setAttribute('next_statuses', $statuses->availableTransitions($order));

        return view('admin.orders.show', [
            'order' => $order->load(['items.menuItem', 'items.variation', 'items.combo', 'statusHistory.user', 'payments']),
        ]);
    }

    public function updateStatus(Request $request, Order $order, OrderStatusService $statuses): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:submitted,accepted,preparing,ready,completed,cancelled'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $statuses->change($order, $validated['status'], $request->user(), $validated['note'] ?? null);

        return back()->with('success', 'Order status updated.');
    }
}
