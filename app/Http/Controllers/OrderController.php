<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\WhatsAppMessageBuilder;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::query()
            ->where('is_active', true)
            ->with(['availableItems' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('order.index', compact('categories'));
    }

    public function store(PlaceOrderRequest $request, WhatsAppMessageBuilder $whatsapp)
    {
        $validated = $request->validated();
        $menuItems = MenuItem::query()
            ->whereIn('id', collect($validated['items'])->pluck('menu_item_id'))
            ->where('is_available', true)
            ->get()
            ->keyBy('id');

        if ($menuItems->isEmpty()) {
            return back()->withErrors(['items' => 'Please add at least one available item.'])->withInput();
        }

        $order = DB::transaction(function () use ($validated, $menuItems) {
            $total = 0;
            $lines = [];

            foreach ($validated['items'] as $row) {
                $item = $menuItems->get($row['menu_item_id']);
                if (! $item) {
                    continue;
                }
                $qty = (int) $row['quantity'];
                $lineTotal = $item->price * $qty;
                $total += $lineTotal;
                $lines[] = [
                    'menu_item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->price,
                    'line_total' => $lineTotal,
                ];
            }

            if ($lines === []) {
                return null;
            }

            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_note' => $validated['customer_note'] ?? null,
                'total_amount' => $total,
                'status' => 'submitted',
            ]);

            foreach ($lines as $line) {
                OrderItem::create(array_merge($line, ['order_id' => $order->id]));
            }

            return $order;
        });

        if (! $order) {
            return back()->withErrors(['items' => 'Could not place order. Check your cart and try again.'])->withInput();
        }

        return redirect()->away($whatsapp->orderUrl($order));
    }
}
