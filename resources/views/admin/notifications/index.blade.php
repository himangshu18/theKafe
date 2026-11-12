@extends('layouts.admin')

@section('heading', 'Notification history')

@section('content')
<div class="space-y-3">
    @forelse($notifications as $notification)
        <article class="flex flex-wrap items-start justify-between gap-4 rounded-xl bg-white p-5 shadow-sm {{ $notification->read_at ? 'opacity-75' : 'ring-1 ring-kafe-200' }}">
            <div>
                <h2 class="font-semibold">{{ $notification->title }}</h2>
                <p class="mt-1 text-sm text-gray-700">{{ $notification->message }}</p>
                <p class="mt-2 text-xs text-gray-500">{{ $notification->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if(($notification->data['order_id'] ?? null))
                    <a href="{{ route('admin.orders.show', $notification->data['order_id']) }}" class="text-sm font-medium text-kafe-800 underline">View order</a>
                @elseif(($notification->data['booking_id'] ?? null))
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-kafe-800 underline">View bookings</a>
                @endif
                @unless($notification->read_at)
                    <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-md border border-kafe-200 px-3 py-1.5 text-xs font-medium text-kafe-800">Mark read</button>
                    </form>
                @endunless
            </div>
        </article>
    @empty
        <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500">No notifications yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
