@extends('layouts.admin')

@section('heading', 'Order queue')

@section('content')
<div class="space-y-4">
    @forelse($orders as $order)
        <article class="rounded-xl bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-kafe-900 hover:underline">{{ $order->public_ref }}</a>
                    <p class="mt-1 text-sm text-gray-700">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }} · {{ $order->items->sum('quantity') }} item(s)</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold">{{ config('kafe.currency') }}{{ number_format((float) $order->total_amount, 2) }}</p>
                    <p class="text-xs capitalize text-gray-500">{{ str_replace('_', ' ', $order->status) }}</p>
                </div>
            </div>

            <ul class="mt-3 space-y-1 text-sm text-gray-600">
                @foreach($order->items as $item)
                    <li>{{ $item->quantity }} × {{ $item->item_name }}</li>
                @endforeach
            </ul>

            @if($order->next_statuses !== [])
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 flex flex-wrap items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" required class="rounded-md border-gray-300 text-sm">
                        @foreach($order->next_statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <input name="note" type="text" maxlength="500" placeholder="Optional note / cancellation reason" class="min-w-52 rounded-md border-gray-300 text-sm">
                    <button class="rounded-md bg-kafe-800 px-3 py-2 text-sm font-semibold text-white hover:bg-kafe-900">Update status</button>
                </form>
            @endif
        </article>
    @empty
        <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500">No orders found.</div>
    @endforelse
</div>

<div class="mt-4">{{ $orders->links() }}</div>
@endsection
