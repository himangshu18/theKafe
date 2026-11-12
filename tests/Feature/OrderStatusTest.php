<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_kitchen_user_can_advance_order_and_the_change_is_recorded(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $kitchen = User::factory()->create(['is_admin' => true, 'admin_role' => 'kitchen']);
        $order = $this->createOrder();

        $this->actingAs($kitchen)
            ->patch(route('admin.orders.status', $order), [
                'status' => 'accepted',
                'note' => 'Kitchen has confirmed the order.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('accepted', $order->refresh()->status);
        $this->assertNotNull($order->accepted_at);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'old_status' => 'submitted',
            'new_status' => 'accepted',
            'changed_by' => $kitchen->id,
            'note' => 'Kitchen has confirmed the order.',
        ]);
        $this->assertDatabaseHas('admin_notifications', [
            'user_id' => $owner->id,
            'type' => 'order_status_changed',
        ]);
    }

    public function test_invalid_transition_does_not_change_order_or_create_notifications(): void
    {
        $kitchen = User::factory()->create(['is_admin' => true, 'admin_role' => 'kitchen']);
        $order = $this->createOrder();

        $this->actingAs($kitchen)
            ->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.status', $order), ['status' => 'preparing'])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertSame('submitted', $order->refresh()->status);
        $this->assertDatabaseCount('order_status_histories', 0);
        $this->assertDatabaseCount('admin_notifications', 0);
    }

    private function createOrder(): Order
    {
        return Order::create([
            'public_ref' => 'KFTESTSTATUS001',
            'customer_name' => 'Test Customer',
            'customer_phone' => '9876543210',
            'total_amount' => '10.00',
            'discount_amount' => '0.00',
            'status' => 'submitted',
            'source' => 'website',
            'order_type' => 'pickup',
            'payment_status' => 'unpaid',
            'idempotency_key' => 'test-order-status-idempotency-key',
            'received_at' => now(),
        ]);
    }
}
