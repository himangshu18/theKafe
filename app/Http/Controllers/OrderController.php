<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Models\Combo;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Services\MenuPricingService;
use App\Services\OrderPlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(MenuPricingService $pricing): View
    {
        $categories = MenuCategory::query()
            ->where('is_active', true)
            ->with([
                'availableItems' => fn ($query) => $query->with([
                    'variations',
                    'addonGroups.options',
                    'availabilitySchedules',
                    'todaySpecials',
                    'offers',
                ])->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get()
            ->each(function (MenuCategory $category): void {
                $category->setRelation(
                    'availableItems',
                    $category->availableItems->filter(fn ($item) => $item->isAvailableAt())->values()
                );
            });

        foreach ($categories->flatMap->availableItems as $item) {
            $itemPrice = $pricing->bestPrice($item, $pricing->toCents($item->price));
            $item->setAttribute('discounted_price', $pricing->formatCents($itemPrice['price_cents']));
            $item->setAttribute('discount_amount_cents', $itemPrice['discount_cents']);
            $item->setAttribute(
                'special_title',
                $item->todaySpecials->first(fn ($special) => $special->isAvailableAt())?->title
            );

            foreach ($item->variations as $variation) {
                $variationPrice = $pricing->bestPrice($item, $pricing->toCents($variation->price));
                $variation->setAttribute('discounted_price', $pricing->formatCents($variationPrice['price_cents']));
            }
        }

        $combos = Combo::query()
            ->with(['groups.eligibleItems.category', 'groups.eligibleItems.availabilitySchedules'])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Combo $combo) => $combo->isAvailableAt())
            ->each(function (Combo $combo): void {
                foreach ($combo->groups as $group) {
                    $group->setRelation(
                        'eligibleItems',
                        $group->eligibleItems
                            ->filter(fn ($item) => $item->category?->is_active && $item->isAvailableAt())
                            ->values()
                    );
                }
            })
            ->values();

        return view('order.index', compact('categories', 'combos'));
    }

    public function store(PlaceOrderRequest $request, OrderPlacementService $orders): RedirectResponse
    {
        $order = $orders->place($request->validated());

        return redirect()->route('order.confirmation', $order->public_ref);
    }

    public function confirmation(Order $order): View
    {
        return view('order.confirmation', [
            'order' => $order->load(['items.menuItem', 'items.variation', 'items.combo']),
        ]);
    }
}
