@extends('layouts.admin')

@section('heading', 'Today’s specials')

@section('content')
<div class="mb-5 flex justify-end"><a href="{{ route('admin.specials.create') }}" class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Add special</a></div>
<div class="space-y-3">
    @forelse($specials as $special)
        <article class="flex flex-wrap items-start justify-between gap-4 rounded-xl bg-white p-5 shadow-sm">
            <div>
                <h2 class="font-semibold">{{ $special->title ?: $special->menuItem?->name }}</h2>
                <p class="text-sm text-gray-600">{{ $special->menuItem?->name }} · @if($special->special_price !== null){{ config('kafe.currency') }}{{ number_format((float) $special->special_price, 2) }}@else No special price@endif</p>
                <p class="text-xs text-gray-500">{{ $special->is_active ? 'Active' : 'Inactive' }} · {{ $special->starts_at?->format('d M, h:i A') ?? 'No start' }} – {{ $special->ends_at?->format('d M, h:i A') ?? 'No end' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.specials.edit', $special) }}" class="text-sm font-medium text-kafe-800 underline">Edit</a>
                <form method="POST" action="{{ route('admin.specials.destroy', $special) }}" onsubmit="return confirm('Remove this special?')">@csrf @method('DELETE')<button class="text-sm text-red-600 underline">Remove</button></form>
            </div>
        </article>
    @empty
        <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500">No specials have been configured.</div>
    @endforelse
</div>
<div class="mt-4">{{ $specials->links() }}</div>
@endsection
