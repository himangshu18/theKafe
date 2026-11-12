<?php

namespace Tests\Feature;

use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\AdminNotification;
use App\Models\Combo;
use App\Models\ComboGroup;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_order_uses_database_prices_and_notifies_admins(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Cafe Latte', '10.00');
        $variation = $item->variations()->create([
            'name' => 'Large',
            'price' => '15.00',
            'is_available' => true,
        ]);
        $group = AddonGroup::create([
            'name' => 'Extras',
            'min_select' => 1,
            'max_select' => 1,
            'is_required' => true,
            'is_active' => true,
        ]);
        $option = AddonOption::create([
            'addon_group_id' => $group->id,
            'name' => 'Extra cream',
            'price_delta' => '2.50',
            'is_available' => true,
        ]);
        $item->addonGroups()->attach($group);

        $response = $this->post(route('order.store'), [
            'customer_name' => 'A Customer',
            'customer_phone' => '9876543210',
            'idempotency_key' => str_repeat('a', 64),
            'items' => [[
                'menu_item_id' => $item->id,
                'menu_variation_id' => $variation->id,
                'addon_option_ids' => [$option->id],
                'quantity' => 2,
                'unit_price' => '0.01',
            ]],
        ]);

        $order = Order::query()->firstOrFail();
        $response->assertRedirect(route('order.confirmation', $order->public_ref));
        $response->assertSessionHasNoErrors();

        $this->assertSame('35.00', $order->total_amount);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('submitted', $order->status);
        $this->assertSame('Large', $order->items->first()->variation_name);
        $this->assertSame('Extra cream', $order->items->first()->customizations['Extras'][0]['name']);
        $this->assertDatabaseHas('admin_notifications', [
            'user_id' => $admin->id,
            'type' => 'new_order',
        ]);
        $this->assertSame(
            'pickup',
            AdminNotification::query()->where('user_id', $admin->id)->firstOrFail()->data['order_type']
        );
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'new_status' => 'submitted',
        ]);
    }

    public function test_unavailable_menu_item_does_not_create_an_order(): void
    {
        $item = $this->createMenuItem('Unavailable Item', '8.00', false);

        $response = $this->from(route('order.index'))->post(route('order.store'), [
            'customer_name' => 'A Customer',
            'customer_phone' => '9876543210',
            'items' => [[
                'menu_item_id' => $item->id,
                'quantity' => 1,
            ]],
        ]);

        $response->assertRedirect(route('order.index'));
        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('admin_notifications', 0);
    }

    public function test_combo_order_snapshots_the_quantity_of_each_included_selection(): void
    {
        $item = $this->createMenuItem('Cold Coffee', '20.00');
        $combo = Combo::create([
            'name' => 'Two Cold Coffees',
            'slug' => 'two-cold-coffees',
            'price' => '50.00',
            'kind' => 'offer',
            'is_active' => true,
        ]);
        $group = ComboGroup::create([
            'combo_id' => $combo->id,
            'name' => 'Choose a coffee',
            'min_select' => 1,
            'max_select' => 1,
            'included_quantity' => 2,
            'sort_order' => 0,
        ]);
        $group->eligibleItems()->attach($item, ['price_adjustment' => '3.50']);

        $this->post(route('order.store'), [
            'customer_name' => 'A Customer',
            'customer_phone' => '9876543210',
            'items' => [[
                'combo_id' => $combo->id,
                'combo_selections' => [$group->id => [$item->id]],
                'quantity' => 1,
            ]],
        ])->assertSessionHasNoErrors();

        $order = Order::query()->with('items')->firstOrFail();

        $this->assertSame('57.00', $order->total_amount);
        $this->assertSame(
            ['Cold Coffee', 'Cold Coffee'],
            $order->items->first()->combo_selections['Choose a coffee']
        );
    }

    public function test_repeated_submission_with_the_same_idempotency_key_creates_one_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Masala Chai', '30.00');
        $payload = [
            'customer_name' => 'A Customer',
            'customer_phone' => '9876543210',
            'idempotency_key' => str_repeat('b', 64),
            'items' => [[
                'menu_item_id' => $item->id,
                'quantity' => 1,
            ]],
        ];

        $this->post(route('order.store'), $payload)->assertSessionHasNoErrors();
        $firstReference = Order::query()->value('public_ref');
        $this->post(route('order.store'), $payload)->assertSessionHasNoErrors();

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('admin_notifications', 1);
        $this->assertSame($firstReference, Order::query()->value('public_ref'));
        $this->assertSame(1, $admin->adminNotifications()->count());
    }

    public function test_kitchen_role_cannot_manage_menu(): void
    {
        $kitchenUser = User::factory()->create([
            'is_admin' => true,
            'admin_role' => 'kitchen',
        ]);

        $this->actingAs($kitchenUser)
            ->get(route('admin.menu.index'))
            ->assertForbidden();
    }

    public function test_menu_page_renders_without_whatsapp_ordering(): void
    {
        $this->createMenuItem('Masala Chai', '30.00');

        $this->get(route('order.index'))
            ->assertOk()
            ->assertSee('Submit order')
            ->assertDontSee('WhatsApp');
    }

    private function createMenuItem(string $name, string $price, bool $isAvailable = true): MenuItem
    {
        $category = MenuCategory::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'is_active' => true,
        ]);

        return MenuItem::create([
            'menu_category_id' => $category->id,
            'name' => $name,
            'price' => $price,
            'food_type' => 'veg',
            'is_available' => $isAvailable,
        ]);
    }
}
