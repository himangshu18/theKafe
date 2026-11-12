@extends('layouts.admin')

@section('heading', 'Dashboard')

@section('content')
<div class="grid gap-6 sm:grid-cols-3">
    @if($menuCount !== null)
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Menu items</p>
        <p class="mt-2 text-3xl font-bold">{{ $menuCount }}</p>
    </div>
    @endif
    @if($pendingBookings !== null)
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Pending bookings</p>
        <p class="mt-2 text-3xl font-bold">{{ $pendingBookings }}</p>
    </div>
    @endif
    @if($pendingOrders !== null)
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <p class="text-sm text-gray-500">Orders awaiting action</p>
        <p class="mt-2 text-3xl font-bold">{{ $pendingOrders }}</p>
    </div>
    @endif
</div>

@if($recentOrders->isNotEmpty())
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
                        <td class="py-2"><a class="font-medium text-kafe-800 underline" href="{{ route('admin.orders.show', $order) }}">{{ $order->public_ref ?? $order->id }}</a></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ config('kafe.currency') }}{{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endif
@endsection
