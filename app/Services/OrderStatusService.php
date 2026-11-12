<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    public function __construct(private AdminNotificationService $notifications) {}

    /**
     * @var array<string, list<string>>
     */
    private const TRANSITIONS = [
        'submitted' => ['accepted', 'cancelled'],
        'accepted' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    /**
     * @return list<string>
     */
    public function availableTransitions(Order $order): array
    {
        return self::TRANSITIONS[$order->status] ?? [];
    }

    public function change(Order $order, string $status, User $actor, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $status, $actor, $note): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $oldStatus = $lockedOrder->status;

            if (! in_array($status, self::TRANSITIONS[$oldStatus] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => sprintf('The order cannot move from %s to %s.', $oldStatus, $status),
                ]);
            }

            $updates = ['status' => $status];
            if ($status === 'accepted') {
                $updates['accepted_at'] = now();
            }
            if ($status === 'cancelled') {
                $updates['cancellation_reason'] = $note;
            }

            $lockedOrder->update($updates);
            $lockedOrder->statusHistory()->create([
                'old_status' => $oldStatus,
                'new_status' => $status,
                'changed_by' => $actor->id,
                'note' => $note,
            ]);

            $notificationType = $status === 'cancelled' ? 'order_cancelled' : 'order_status_changed';
            $this->notifications->notifyOrder(
                $lockedOrder,
                $notificationType,
                $status === 'cancelled' ? 'Order cancelled' : 'Order status updated',
                sprintf('Order %s is now %s.', $lockedOrder->public_ref, str_replace('_', ' ', $status))
            );

            return $lockedOrder;
        });
    }
}
