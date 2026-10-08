<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\WhatsAppMessageBuilder;

class BookingController extends Controller
{
    public function create()
    {
        return view('booking.create', [
            'partyTypes' => config('kafe.party_types'),
            'timeSlots' => config('kafe.booking_time_slots'),
        ]);
    }

    public function store(StoreBookingRequest $request, WhatsAppMessageBuilder $whatsapp)
    {
        $booking = Booking::create($request->validated());

        return redirect()->away($whatsapp->bookingUrl($booking));
    }
}
