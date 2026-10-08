@extends('layouts.admin')

@section('heading', 'Dashboard')

@section('content')
<div class="grid gap-6 sm:grid-cols-3">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Menu items</p>
        <p class="mt-2 text-3xl font-bold">{{ $menuCount }}</p>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Pending bookings</p>
        <p class="mt-2 text-3xl font-bold">{{ $pendingBookings }}</p>
    </div>
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Owner WhatsApp</p>
        @if(config('kafe.whatsapp_number'))
            <p class="mt-2 font-mono text-lg font-semibold text-kafe-800">{{ config('kafe.whatsapp_number') }}</p>
            <p class="mt-1 text-xs text-gray-500">Change <code class="rounded bg-gray-100 px-1">KAFE_OWNER_PHONE</code> in <code class="rounded bg-gray-100 px-1">.env</code>, then <code class="rounded bg-gray-100 px-1">php artisan config:clear</code></p>
        @else
            <p class="mt-2 text-sm text-amber-700">Not set — add <code class="rounded bg-amber-50 px-1">KAFE_OWNER_PHONE=91XXXXXXXXXX</code> to <code class="rounded bg-amber-50 px-1">.env</code></p>
        @endif
    </div>
</div>

<div class="mt-8 rounded-xl bg-white p-6 shadow-sm">
    <h2 class="font-semibold">Recent orders</h2>
    @if($recentOrders->isEmpty())
        <p class="mt-4 text-sm text-gray-500">No orders yet.</p>
    @else
        <table class="mt-4 w-full text-left text-sm">
            <thead>
                <tr class="border-b text-gray-500">
                    <th class="py-2">#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                    <tr class="border-b border-gray-100">
                        <td class="py-2">{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ config('kafe.currency') }}{{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
