@extends('layouts.admin')

@section('heading', 'Categories')

@section('content')
<div class="mb-6 rounded-xl bg-white p-6 shadow-sm">
    <h2 class="font-semibold">New category</h2>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 flex flex-wrap items-end gap-4">
        @csrf
        <div>
            <label class="text-sm font-medium">Name</label>
            <input type="text" name="name" required class="mt-1 rounded-md border-gray-300">
        </div>
        <div>
            <label class="text-sm font-medium">Sort</label>
            <input type="number" name="sort_order" value="0" class="mt-1 w-24 rounded-md border-gray-300">
        </div>
        <button type="submit" class="rounded-lg bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Add</button>
    </form>
</div>

<div class="space-y-4">
    @foreach($categories as $category)
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-wrap items-center gap-4">
                @csrf
                @method('PUT')
                <input type="text" name="name" value="{{ $category->name }}" class="rounded-md border-gray-300">
                <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-20 rounded-md border-gray-300">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active
                </label>
                <span class="text-sm text-gray-500">{{ $category->items_count }} items</span>
                <button type="submit" class="text-sm font-medium text-kafe-800">Save</button>
            </form>
            @if($category->items_count === 0)
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-2" onsubmit="return confirm('Delete category?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600">Delete empty category</button>
                </form>
            @endif
        </div>
    @endforeach
</div>
@endsection
