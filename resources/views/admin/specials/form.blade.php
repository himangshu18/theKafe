@extends('layouts.admin')

@section('heading', $special->exists ? 'Edit today’s special' : 'Add today’s special')

@section('content')
<form method="POST" action="{{ $special->exists ? route('admin.specials.update', $special) : route('admin.specials.store') }}" class="space-y-5 rounded-xl bg-white p-6 shadow-sm">
    @csrf
    @if($special->exists) @method('PUT') @endif
    <label class="block text-sm font-medium">Menu item<select name="menu_item_id" required class="mt-1 w-full rounded-md border-gray-300">
        @foreach($menuItems as $item)<option value="{{ $item->id }}" @selected(old('menu_item_id', $special->menu_item_id) == $item->id)>{{ $item->name }} · {{ config('kafe.currency') }}{{ number_format((float) $item->price, 2) }}</option>@endforeach
    </select></label>
    <label class="block text-sm font-medium">Display title<input name="title" maxlength="120" value="{{ old('title', $special->title) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
    <label class="block text-sm font-medium">Special price (optional)<input name="special_price" type="number" min="0" step="0.01" value="{{ old('special_price', $special->special_price) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="text-sm font-medium">Starts at<input name="starts_at" type="datetime-local" value="{{ old('starts_at', $special->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Ends at<input name="ends_at" type="datetime-local" value="{{ old('ends_at', $special->ends_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
    </div>
    <label class="flex items-center gap-2 text-sm"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $special->exists ? $special->is_active : true))> Active</label>
    <div class="flex justify-between">
        <a href="{{ route('admin.specials.index') }}" class="text-sm text-kafe-800 underline">Back to specials</a>
        <button class="rounded-md bg-kafe-800 px-5 py-2 font-semibold text-white">Save special</button>
    </div>
</form>
@endsection
