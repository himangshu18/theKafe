<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>|null  $roles
     */
    public function notifyAdmins(
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?array $roles = null
    ): void {
        $recipients = User::query()
            ->where('is_admin', true)
            ->when($roles !== null, fn ($query) => $query->whereIn('admin_role', $roles))
            ->get(['id']);

        foreach ($recipients as $recipient) {
            AdminNotification::create([
                'user_id' => $recipient->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function notifyOrder(Model $order, string $type, string $title, string $message): void
    {
        $this->notifyAdmins(
            $type,
            $title,
            $message,
            [
                'order_id' => $order->getKey(),
                'public_ref' => $order->getAttribute('public_ref'),
                'order_type' => $order->getAttribute('order_type'),
            ],
            ['owner', 'manager', 'cashier', 'kitchen']
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function notifyBooking(Model $booking, string $type, string $title, string $message): void
    {
        $this->notifyAdmins(
            $type,
            $title,
            $message,
            ['booking_id' => $booking->getKey()],
            ['owner', 'manager', 'cashier']
        );
    }
}
