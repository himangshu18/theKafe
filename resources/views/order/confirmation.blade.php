@extends('layouts.kafe')

@section('title', 'Order received — '.config('kafe.name'))

@section('content')
<section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="rounded-2xl border border-kafe-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Order received</p>
        <h1 class="mt-2 font-display text-3xl font-semibold text-kafe-950">Thank you, {{ $order->customer_name }}.</h1>
        <p class="mt-2 text-kafe-700">The café has received your order. Please wait for the team to confirm its preparation.</p>

        <dl class="mt-6 grid gap-4 border-y border-kafe-100 py-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-kafe-500">Order reference</dt>
                <dd class="mt-1 font-mono font-semibold text-kafe-900">{{ $order->public_ref }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-kafe-500">Status</dt>
                <dd class="mt-1 font-medium capitalize text-kafe-900">{{ str_replace('_', ' ', $order->status) }}</dd>
            </div>
        </dl>

        <ul class="mt-5 space-y-3">
            @foreach($order->items as $item)
                <li class="flex justify-between gap-4 text-sm">
                    <div>
                        <p class="font-medium text-kafe-900">{{ $item->quantity }} × {{ $item->item_name }}</p>
                        @if($item->variation_name)
                            <p class="text-kafe-600">{{ $item->variation_name }}</p>
                        @endif
                        @if($item->customizations)
                            <p class="text-kafe-600">{{ collect($item->customizations)->flatten(1)->pluck('name')->join(', ') }}</p>
                        @endif
                        @if($item->combo_selections)
                            @foreach($item->combo_selections as $groupName => $names)
                                <p class="text-kafe-600">{{ $groupName }}: {{ implode(', ', $names) }}</p>
                            @endforeach
                        @endif
                    </div>
                    <span class="whitespace-nowrap font-medium text-kafe-900">{{ config('kafe.currency') }}{{ number_format((float) $item->line_total, 2) }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-5 flex justify-between border-t border-kafe-100 pt-4 font-semibold text-kafe-950">
            <span>Total due at café</span>
            <span>{{ config('kafe.currency') }}{{ number_format((float) $order->total_amount, 2) }}</span>
        </div>
        <p class="mt-3 text-xs text-kafe-600">Your phone number: {{ $order->customer_phone }}. No online payment was taken.</p>
        <a href="{{ route('order.index') }}" class="btn-primary mt-6 inline-flex">Back to menu</a>
    </div>
</section>
@endsection
