@extends('layouts.admin')

@section('heading', 'Order '.$order->public_ref)

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <section class="space-y-6 lg:col-span-2">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex flex-wrap justify-between gap-4">
                <div>
                    <h2 class="font-semibold">{{ $order->customer_name }}</h2>
                    <p class="text-sm text-gray-600">{{ $order->customer_phone }}</p>
                    @if($order->customer_note)
                        <p class="mt-2 text-sm text-gray-700">{{ $order->customer_note }}</p>
                    @endif
                </div>
                <p class="capitalize text-gray-600">{{ str_replace('_', ' ', $order->status) }}</p>
            </div>

            <ul class="mt-6 divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $item->quantity }} × {{ $item->item_name }}</p>
                            @if($item->variation_name)<p class="text-gray-500">{{ $item->variation_name }}</p>@endif
                            @if($item->customizations)
                                <p class="text-gray-500">{{ collect($item->customizations)->flatten(1)->pluck('name')->join(', ') }}</p>
                            @endif
                            @if($item->combo_selections)
                                @foreach($item->combo_selections as $groupName => $names)
                                    <p class="text-gray-500">{{ $groupName }}: {{ implode(', ', $names) }}</p>
                                @endforeach
                            @endif
                        </div>
                        <span class="whitespace-nowrap">{{ config('kafe.currency') }}{{ number_format((float) $item->line_total, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 flex justify-between border-t pt-4 font-semibold">
                <span>Total</span>
                <span>{{ config('kafe.currency') }}{{ number_format((float) $order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Status timeline</h2>
            <ol class="mt-4 space-y-3">
                @foreach($order->statusHistory as $history)
                    <li class="border-l-2 border-kafe-200 pl-4 text-sm">
                        <p class="font-medium capitalize">{{ str_replace('_', ' ', $history->new_status) }}</p>
                        <p class="text-xs text-gray-500">{{ $history->created_at->format('d M Y, h:i A') }} @if($history->user) · {{ $history->user->name }} @endif</p>
                        @if($history->note)<p class="mt-1 text-gray-600">{{ $history->note }}</p>@endif
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <aside class="space-y-6">
        @if($order->next_statuses !== [])
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="rounded-xl bg-white p-6 shadow-sm">
                @csrf
                @method('PATCH')
                <h2 class="font-semibold">Update order</h2>
                <label for="status" class="mt-4 block text-sm text-gray-600">Next status</label>
                <select id="status" name="status" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    @foreach($order->next_statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <label for="note" class="mt-3 block text-sm text-gray-600">Note / cancellation reason</label>
                <textarea id="note" name="note" maxlength="500" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                <button class="mt-4 w-full rounded-md bg-kafe-800 px-3 py-2 text-sm font-semibold text-white">Save status</button>
            </form>
        @endif

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Payments</h2>
            @forelse($order->payments as $payment)
                <p class="mt-2 text-sm text-gray-600">{{ config('kafe.currency') }}{{ number_format((float) $payment->amount, 2) }} · {{ ucfirst($payment->method) }} · {{ $payment->paid_at->format('d M, h:i A') }}</p>
            @empty
                <p class="mt-2 text-sm text-gray-500">No payment recorded.</p>
            @endforelse
        </div>
    </aside>
</div>
@endsection
