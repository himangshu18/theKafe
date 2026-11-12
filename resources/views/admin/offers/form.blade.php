@extends('layouts.admin')

@section('heading', $offer->exists ? 'Edit '.$offer->name : 'Create offer')

@section('content')
<form method="POST" action="{{ $offer->exists ? route('admin.offers.update', $offer) : route('admin.offers.store') }}" class="space-y-5 rounded-xl bg-white p-6 shadow-sm">
    @csrf
    @if($offer->exists) @method('PUT') @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="text-sm font-medium">Name<input name="name" required maxlength="160" value="{{ old('name', $offer->name) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Discount type<select name="discount_type" class="mt-1 w-full rounded-md border-gray-300"><option value="percentage" @selected(old('discount_type', $offer->discount_type) === 'percentage')>Percentage</option><option value="fixed" @selected(old('discount_type', $offer->discount_type) === 'fixed')>Fixed amount</option></select></label>
        <label class="text-sm font-medium">Discount value<input name="discount_value" type="number" min="0.01" step="0.01" required value="{{ old('discount_value', $offer->discount_value) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="flex items-center gap-2 pt-6 text-sm"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $offer->exists ? $offer->is_active : true))> Active</label>
        <label class="text-sm font-medium">Starts at<input name="starts_at" type="datetime-local" value="{{ old('starts_at', $offer->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Ends at<input name="ends_at" type="datetime-local" value="{{ old('ends_at', $offer->ends_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium sm:col-span-2">Description<textarea name="description" rows="2" maxlength="1000" class="mt-1 w-full rounded-md border-gray-300">{{ old('description', $offer->description) }}</textarea></label>
    </div>
    <fieldset>
        <legend class="text-sm font-semibold">Eligible menu items</legend>
        <div class="mt-2 grid max-h-80 gap-2 overflow-auto sm:grid-cols-2 lg:grid-cols-3">
            @foreach($menuItems as $item)
                <label class="flex items-center gap-2 rounded border border-gray-100 p-2 text-sm">
                    <input type="checkbox" name="menu_item_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, old('menu_item_ids', $offer->menuItems->pluck('id')->all()), false))>
                    <span>{{ $item->name }} · {{ config('kafe.currency') }}{{ number_format((float) $item->price, 2) }}</span>
                </label>
            @endforeach
        </div>
    </fieldset>
    <div class="flex justify-between">
        <a href="{{ route('admin.offers.index') }}" class="text-sm text-kafe-800 underline">Back to offers</a>
        <button class="rounded-md bg-kafe-800 px-5 py-2 font-semibold text-white">Save offer</button>
    </div>
</form>
@endsection
