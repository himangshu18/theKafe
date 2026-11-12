<?php

namespace Tests\Feature;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_booking_notifications_are_limited_to_booking_staff(): void
    {
        $this->freezeTime();
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $cashier = User::factory()->create(['is_admin' => true, 'admin_role' => 'cashier']);
        $kitchen = User::factory()->create(['is_admin' => true, 'admin_role' => 'kitchen']);
        $accountant = User::factory()->create(['is_admin' => true, 'admin_role' => 'accountant']);

        $this->post(route('booking.store'), [
            'customer_name' => 'Test Customer',
            'customer_phone' => '9876543210',
            'party_type' => 'birthday',
            'event_date' => today()->toDateString(),
            'event_time' => '14:00',
            'guest_count' => 12,
        ])->assertRedirect(route('booking.create'));

        $this->assertDatabaseHas('admin_notifications', [
            'user_id' => $owner->id,
            'type' => 'new_booking',
        ]);
        $this->assertDatabaseHas('admin_notifications', [
            'user_id' => $cashier->id,
            'type' => 'new_booking',
        ]);
        $this->assertDatabaseMissing('admin_notifications', [
            'user_id' => $kitchen->id,
            'type' => 'new_booking',
        ]);
        $this->assertDatabaseMissing('admin_notifications', [
            'user_id' => $accountant->id,
            'type' => 'new_booking',
        ]);

        $this->actingAs($kitchen)
            ->getJson(route('admin.notifications.poll'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0)
            ->assertJsonPath('items', []);
    }

    public function test_admin_cannot_mark_another_admins_notification_as_read(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $cashier = User::factory()->create(['is_admin' => true, 'admin_role' => 'cashier']);
        $notification = AdminNotification::create([
            'user_id' => $owner->id,
            'type' => 'new_order',
            'title' => 'New order received',
            'message' => 'A test order was submitted.',
        ]);

        $this->actingAs($cashier)
            ->patch(route('admin.notifications.read', $notification))
            ->assertNotFound();

        $this->assertNull($notification->refresh()->read_at);

        $this->actingAs($owner)
            ->patch(route('admin.notifications.read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->refresh()->read_at);
    }

    public function test_poll_includes_order_type_for_order_notifications(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        AdminNotification::create([
            'user_id' => $owner->id,
            'type' => 'new_order',
            'title' => 'New order received',
            'message' => 'A test order was submitted.',
            'data' => ['order_id' => 1, 'order_type' => 'pickup'],
        ]);

        $this->actingAs($owner)
            ->getJson(route('admin.notifications.poll'))
            ->assertOk()
            ->assertJsonPath('items.0.order_type', 'pickup');
    }

    public function test_admin_layout_enables_notification_sound_by_default(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('soundEnabled: true', false);
    }
}
