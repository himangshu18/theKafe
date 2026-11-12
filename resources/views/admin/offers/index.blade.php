@extends('layouts.admin')

@section('heading', 'Offers and discounts')

@section('content')
<div class="mb-5 flex justify-end"><a href="{{ route('admin.offers.create') }}" class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Create offer</a></div>
<div class="space-y-3">
    @forelse($offers as $offer)
        <article class="flex flex-wrap items-start justify-between gap-4 rounded-xl bg-white p-5 shadow-sm">
            <div>
                <h2 class="font-semibold">{{ $offer->name }}</h2>
                <p class="text-sm text-gray-600">{{ $offer->discount_value }}{{ $offer->discount_type === 'percentage' ? '%' : ' off' }} · {{ $offer->is_active ? 'Active' : 'Inactive' }}</p>
                <p class="mt-1 text-xs text-gray-500">Applies to: {{ $offer->menuItems->pluck('name')->join(', ') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.offers.edit', $offer) }}" class="text-sm font-medium text-kafe-800 underline">Edit</a>
                <form method="POST" action="{{ route('admin.offers.destroy', $offer) }}" onsubmit="return confirm('Remove this offer?')">@csrf @method('DELETE')<button class="text-sm text-red-600 underline">Remove</button></form>
            </div>
        </article>
    @empty
        <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500">No offers have been configured.</div>
    @endforelse
</div>
<div class="mt-4">{{ $offers->links() }}</div>
@endsection
