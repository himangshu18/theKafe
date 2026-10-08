@extends('layouts.admin')

@section('heading', 'Party bookings')

@section('content')
<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="border-b bg-gray-50 text-gray-600">
            <tr>
                <th class="px-4 py-3">Guest</th>
                <th>Occasion</th>
                <th>When</th>
                <th>Guests</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr class="border-b border-gray-100 align-top">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $booking->customer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->customer_phone }}</p>
                        @if($booking->message)
                            <p class="mt-1 text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($booking->message, 80) }}</p>
                        @endif
                    </td>
                    <td class="py-3">{{ config('kafe.party_types.'.$booking->party_type, $booking->party_type) }}</td>
                    <td class="py-3">{{ $booking->event_date->format('d M Y') }} · {{ config('kafe.booking_time_slots.'.$booking->event_time, $booking->event_time) }}</td>
                    <td class="py-3">{{ $booking->guest_count }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 text-xs">
                                @foreach(['pending', 'confirmed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No bookings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
