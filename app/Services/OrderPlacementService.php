<?php

namespace App\Services;

use App\Models\Combo;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderPlacementService
{
    public function __construct(
        private MenuPricingService $pricing,
        private AdminNotificationService $notifications,
    ) {}

    /**
     * @param array{
     *     customer_name: string,
     *     customer_phone: string,
     *     customer_note?: ?string,
     *     items: list<array<string, mixed>>,
     *     idempotency_key?: ?string
     * } $details
     */
    public function place(array $details): Order
    {
        $idempotencyKey = $details['idempotency_key'] ?? Str::random(64);

        try {
            return DB::transaction(function () use ($details, $idempotencyKey): Order {
                $existingOrder = Order::query()->where('idempotency_key', $idempotencyKey)->first();

                if ($existingOrder) {
                    return $existingOrder->load('items');
                }

                $order = Order::create([
                    'public_ref' => $this->publicReference(),
                    'customer_name' => $details['customer_name'],
                    'customer_phone' => $details['customer_phone'],
                    'customer_note' => $details['customer_note'] ?? null,
                    'total_amount' => '0.00',
                    'discount_amount' => '0.00',
                    'status' => 'submitted',
                    'source' => 'website',
                    'order_type' => 'pickup',
                    'payment_status' => 'unpaid',
                    'idempotency_key' => $idempotencyKey,
                    'received_at' => now(),
                ]);

                $totalCents = 0;
                $discountCents = 0;

                foreach ($details['items'] as $line) {
                    $pricedLine = $this->priceLine($line);
                    $lineTotal = $pricedLine['unit_price_cents'] * $line['quantity'];
                    $lineDiscount = $pricedLine['discount_cents'] * $line['quantity'];
                    $totalCents += $lineTotal;
                    $discountCents += $lineDiscount;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $pricedLine['menu_item_id'],
                        'item_name' => $pricedLine['item_name'],
                        'menu_variation_id' => $pricedLine['variation_id'],
                        'combo_id' => $pricedLine['combo_id'],
                        'offer_id' => $pricedLine['offer_id'],
                        'variation_name' => $pricedLine['variation_name'],
                        'customizations' => $pricedLine['customizations'],
                        'combo_name' => $pricedLine['combo_name'],
                        'combo_selections' => $pricedLine['combo_selections'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $this->pricing->formatCents($pricedLine['unit_price_cents']),
                        'line_total' => $this->pricing->formatCents($lineTotal),
                        'discount_amount' => $this->pricing->formatCents($lineDiscount),
                    ]);
                }

                $order->update([
                    'total_amount' => $this->pricing->formatCents($totalCents),
                    'discount_amount' => $this->pricing->formatCents($discountCents),
                ]);

                $order->statusHistory()->create([
                    'new_status' => 'submitted',
                    'note' => 'Order submitted by customer.',
                ]);

                $this->notifications->notifyOrder(
                    $order,
                    'new_order',
                    'New order received',
                    sprintf('Order %s was submitted by %s.', $order->public_ref, $order->customer_name)
                );

                return $order->load('items');
            }, attempts: 3);
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'idempotency_key')) {
                $existingOrder = Order::query()->where('idempotency_key', $idempotencyKey)->first();
                if ($existingOrder) {
                    return $existingOrder->load('items');
                }
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array{
     *     item_name: string,
     *     menu_item_id: ?int,
     *     variation_id: ?int,
     *     variation_name: ?string,
     *     combo_id: ?int,
     *     combo_name: ?string,
     *     combo_selections: ?array<string, list<string>>,
     *     customizations: ?array<string, mixed>,
     *     offer_id: ?int,
     *     unit_price_cents: int,
     *     discount_cents: int
     * }
     */
    private function priceLine(array $line): array
    {
        if (! empty($line['combo_id'])) {
            if (! empty($line['menu_item_id'])) {
                throw ValidationException::withMessages([
                    'items' => 'Each order line must contain either a menu item or a combo.',
                ]);
            }

            return $this->priceCombo($line);
        }

        if (empty($line['menu_item_id'])) {
            throw ValidationException::withMessages([
                'items' => 'Each order line must contain a menu item or a combo.',
            ]);
        }

        $item = MenuItem::query()
            ->with([
                'category',
                'variations',
                'addonGroups.options',
                'availabilitySchedules',
                'todaySpecials',
                'offers',
            ])
            ->lockForUpdate()
            ->find($line['menu_item_id']);

        if (! $item || ! $item->category?->is_active || ! $item->isAvailableAt()) {
            throw ValidationException::withMessages([
                'items' => 'One or more selected menu items are no longer available.',
            ]);
        }

        $variation = null;
        if (! empty($line['menu_variation_id'])) {
            $variation = $item->variations->firstWhere('id', (int) $line['menu_variation_id']);
            if (! $variation || ! $variation->is_available) {
                throw ValidationException::withMessages([
                    'items' => sprintf('The selected variation for %s is unavailable.', $item->name),
                ]);
            }
        }

        $baseCents = $this->pricing->toCents($variation?->price ?? $item->price);
        [$addonCents, $customizations] = $this->priceAddons($item, $line['addon_option_ids'] ?? []);
        $promotion = $this->pricing->bestPrice($item, $baseCents);

        return [
            'item_name' => $item->name,
            'menu_item_id' => $item->id,
            'variation_id' => $variation?->id,
            'variation_name' => $variation?->name,
            'combo_id' => null,
            'combo_name' => null,
            'combo_selections' => null,
            'customizations' => $customizations,
            'offer_id' => $promotion['offer_id'],
            'unit_price_cents' => $promotion['price_cents'] + $addonCents,
            'discount_cents' => $promotion['discount_cents'],
        ];
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array{
     *     item_name: string,
     *     menu_item_id: ?int,
     *     variation_id: ?int,
     *     variation_name: ?string,
     *     combo_id: ?int,
     *     combo_name: ?string,
     *     combo_selections: ?array<string, list<string>>,
     *     customizations: ?array<string, mixed>,
     *     offer_id: ?int,
     *     unit_price_cents: int,
     *     discount_cents: int
     * }
     */
    private function priceCombo(array $line): array
    {
        $combo = Combo::query()
            ->with(['groups.eligibleItems.category', 'groups.eligibleItems.availabilitySchedules'])
            ->lockForUpdate()
            ->find($line['combo_id']);

        if (! $combo || ! $combo->isAvailableAt()) {
            throw ValidationException::withMessages([
                'items' => 'One or more selected combos are no longer available.',
            ]);
        }

        $selections = $line['combo_selections'] ?? [];
        $snapshot = [];
        $unitPriceCents = $this->pricing->toCents($combo->price);

        foreach ($combo->groups as $group) {
            $selectedIds = array_values(array_unique(array_map(
                'intval',
                $selections[$group->id] ?? []
            )));

            if (count($selectedIds) < $group->min_select || count($selectedIds) > $group->max_select) {
                throw ValidationException::withMessages([
                    'items' => sprintf('Please choose the required items for the %s combo.', $combo->name),
                ]);
            }

            $eligible = $group->eligibleItems->keyBy('id');
            foreach ($selectedIds as $selectedId) {
                $selectedItem = $eligible->get($selectedId);
                if (! $selectedItem || ! $selectedItem->category?->is_active || ! $selectedItem->isAvailableAt()) {
                    throw ValidationException::withMessages([
                        'items' => sprintf('A selection in the %s combo is no longer available.', $combo->name),
                    ]);
                }

                $unitPriceCents += $this->pricing->toCents((string) $selectedItem->pivot->price_adjustment)
                    * $group->included_quantity;
                for ($quantity = 0; $quantity < $group->included_quantity; $quantity++) {
                    $snapshot[$group->name][] = $selectedItem->name;
                }
            }
        }

        if ($unitPriceCents < 0) {
            throw ValidationException::withMessages([
                'items' => sprintf('The selected items make the %s combo price invalid.', $combo->name),
            ]);
        }

        return [
            'item_name' => $combo->name,
            'menu_item_id' => null,
            'variation_id' => null,
            'variation_name' => null,
            'combo_id' => $combo->id,
            'combo_name' => $combo->name,
            'combo_selections' => $snapshot,
            'customizations' => null,
            'offer_id' => null,
            'unit_price_cents' => $unitPriceCents,
            'discount_cents' => 0,
        ];
    }

    /**
     * @param  list<int|string>  $selectedIds
     * @return array{int, array<string, list<array{name: string, price: string}>>}
     */
    private function priceAddons(MenuItem $item, array $selectedIds): array
    {
        $selectedIds = array_values(array_unique(array_map('intval', $selectedIds)));
        $activeGroups = $item->addonGroups->where('is_active', true);
        $optionsById = $activeGroups
            ->flatMap(fn ($group) => $group->options)
            ->where('is_available', true)
            ->keyBy('id');

        if (array_diff($selectedIds, $optionsById->keys()->all()) !== []) {
            throw ValidationException::withMessages([
                'items' => sprintf('An add-on selected for %s is not available.', $item->name),
            ]);
        }

        $totalCents = 0;
        $snapshot = [];

        foreach ($activeGroups as $group) {
            $selectedOptions = $group->options
                ->whereIn('id', $selectedIds)
                ->where('is_available', true);
            $count = $selectedOptions->count();
            $minimum = max($group->min_select, $group->is_required ? 1 : 0);

            if ($count < $minimum || $count > $group->max_select) {
                throw ValidationException::withMessages([
                    'items' => sprintf('Please choose the required add-ons for %s.', $item->name),
                ]);
            }

            foreach ($selectedOptions as $option) {
                $totalCents += $this->pricing->toCents($option->price_delta);
                $snapshot[$group->name][] = [
                    'name' => $option->name,
                    'price' => (string) $option->price_delta,
                ];
            }
        }

        return [$totalCents, $snapshot];
    }

    private function publicReference(): string
    {
        do {
            $reference = 'KF'.now()->format('ymd').Str::upper(Str::random(16));
        } while (Order::query()->where('public_ref', $reference)->exists());

        return $reference;
    }
}
