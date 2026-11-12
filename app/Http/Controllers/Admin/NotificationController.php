<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->adminNotifications()
            ->paginate(30);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function poll(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->adminNotifications()
            ->whereNull('read_at')
            ->limit(20)
            ->get()
            ->map(fn (AdminNotification $notification): array => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'order_type' => $notification->data['order_type'] ?? null,
                'created_at' => $notification->created_at->toIso8601String(),
                'url' => $this->notificationUrl($notification),
            ]);

        return response()->json([
            'unread_count' => $request->user()->adminNotifications()->whereNull('read_at')->count(),
            'items' => $notifications,
        ]);
    }

    public function markRead(Request $request, AdminNotification $notification): RedirectResponse|JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 404);
        $notification->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['unread_count' => $request->user()->adminNotifications()->whereNull('read_at')->count()]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    private function notificationUrl(AdminNotification $notification): string
    {
        $data = $notification->data ?? [];

        if (! empty($data['order_id'])) {
            return route('admin.orders.show', $data['order_id']);
        }

        if (! empty($data['booking_id'])) {
            return route('admin.bookings.index');
        }

        return route('admin.notifications.index');
    }
}
