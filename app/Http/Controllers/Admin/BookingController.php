<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking, AdminNotificationService $notifications)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled'],
        ]);

        DB::transaction(function () use ($booking, $validated, $notifications): void {
            $oldStatus = $booking->status;
            $booking->update(['status' => $validated['status']]);

            if ($oldStatus !== $booking->status) {
                $notifications->notifyBooking(
                    $booking,
                    $booking->status === 'cancelled' ? 'booking_cancelled' : 'booking_status_changed',
                    $booking->status === 'cancelled' ? 'Booking cancelled' : 'Booking status updated',
                    sprintf('Booking %d is now %s.', $booking->id, $booking->status)
                );
            }
        });

        return back()->with('success', 'Booking status updated.');
    }
}
