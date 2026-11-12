<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('booking.create', [
            'partyTypes' => config('kafe.party_types'),
            'timeSlots' => config('kafe.booking_time_slots'),
        ]);
    }

    public function store(StoreBookingRequest $request, AdminNotificationService $notifications): RedirectResponse
    {
        DB::transaction(function () use ($request, $notifications): void {
            $booking = Booking::create($request->validated());
            $notifications->notifyBooking(
                $booking,
                'new_booking',
                'New party booking',
                sprintf('%s requested a party booking for %s guests.', $booking->customer_name, $booking->guest_count)
            );
        });

        return redirect()->route('booking.create')->with('success', 'Your booking request has been sent to the café.');
    }
}
