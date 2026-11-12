@extends('layouts.admin')

@section('heading', 'Combos and meal builder')

@section('content')
<div class="mb-5 flex justify-end">
    <a href="{{ route('admin.combos.create') }}" class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Create combo</a>
</div>
<div class="space-y-4">
    @forelse($combos as $combo)
        <article class="rounded-xl bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-semibold">{{ $combo->name }}</h2>
                    <p class="text-sm text-gray-500">{{ ucfirst($combo->kind) }} · {{ $combo->is_active ? 'Active' : 'Inactive' }} · {{ config('kafe.currency') }}{{ number_format((float) $combo->price, 2) }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.combos.edit', $combo) }}" class="text-sm font-medium text-kafe-800 underline">Edit</a>
                    <form method="POST" action="{{ route('admin.combos.destroy', $combo) }}" onsubmit="return confirm('Remove this combo?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-600 underline">Remove</button>
                    </form>
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach($combo->groups as $group)
                    <div class="rounded-lg border border-gray-100 p-3 text-sm">
                        <p class="font-medium">{{ $group->name }} · choose {{ $group->min_select }}–{{ $group->max_select }}</p>
                        <p class="mt-1 text-gray-500">{{ $group->eligibleItems->pluck('name')->join(', ') }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    @empty
        <div class="rounded-xl bg-white p-8 text-center text-sm text-gray-500">No combos created yet.</div>
    @endforelse
</div>
<div class="mt-4">{{ $combos->links() }}</div>
@endsection
