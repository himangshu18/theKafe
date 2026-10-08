@extends('layouts.admin')

@section('heading', 'Menu items')

@section('content')
<div class="mb-8 rounded-xl bg-white p-6 shadow-sm">
    <h2 class="font-semibold">Add menu item</h2>
    <form method="POST" action="{{ route('admin.menu.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-4 sm:grid-cols-2">
        @csrf
        <div>
            <label class="text-sm font-medium">Category</label>
            <select name="menu_category_id" required class="mt-1 w-full rounded-md border-gray-300">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Name</label>
            <input type="text" name="name" required class="mt-1 w-full rounded-md border-gray-300">
        </div>
        <div>
            <label class="text-sm font-medium">Price ({{ config('kafe.currency') }})</label>
            <input type="number" step="0.01" name="price" required class="mt-1 w-full rounded-md border-gray-300">
        </div>
        <div>
            <label class="text-sm font-medium">Sort order</label>
            <input type="number" name="sort_order" value="0" class="mt-1 w-full rounded-md border-gray-300">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Description</label>
            <textarea name="description" rows="2" class="mt-1 w-full rounded-md border-gray-300"></textarea>
        </div>
        <div>
            <label class="text-sm font-medium">Photo</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm">
        </div>
        <div class="flex flex-wrap items-center gap-4 pt-6">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_available" value="1" checked> Available</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1"> Featured on home</label>
        </div>
        <div class="sm:col-span-2">
            <button type="submit" class="rounded-lg bg-kafe-800 px-4 py-2 text-sm font-semibold text-white hover:bg-kafe-900">Save item</button>
        </div>
    </form>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full min-w-[640px] text-left text-sm">
        <thead class="border-b bg-gray-50 text-gray-600">
            <tr>
                <th class="px-4 py-3">Item</th>
                <th>Category</th>
                <th>Price</th>
                <th>Flags</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr class="border-b border-gray-100 align-top">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $item->name }}</p>
                        <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</p>
                    </td>
                    <td class="py-3">{{ $item->category->name }}</td>
                    <td class="py-3">{{ config('kafe.currency') }}{{ number_format($item->price, 2) }}</td>
                    <td class="py-3 text-xs">
                        @if($item->is_available)<span class="text-emerald-600">Available</span>@else<span class="text-red-600">Hidden</span>@endif
                        @if($item->is_featured) · Featured @endif
                    </td>
                    <td class="px-4 py-3">
                        <details class="text-sm">
                            <summary class="cursor-pointer font-medium text-kafe-800">Edit</summary>
                            <form method="POST" action="{{ route('admin.menu.update', $item) }}" enctype="multipart/form-data" class="mt-2 space-y-2 rounded border border-gray-200 p-3">
                                @csrf
                                @method('PUT')
                                <select name="menu_category_id" class="w-full rounded-md border-gray-300 text-sm">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected($cat->id === $item->menu_category_id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="name" value="{{ $item->name }}" class="w-full rounded-md border-gray-300 text-sm">
                                <input type="number" step="0.01" name="price" value="{{ $item->price }}" class="w-full rounded-md border-gray-300 text-sm">
                                <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ $item->description }}</textarea>
                                <input type="file" name="image" accept="image/*" class="w-full text-xs">
                                <label class="flex gap-2 text-xs"><input type="checkbox" name="is_available" value="1" @checked($item->is_available)> Available</label>
                                <label class="flex gap-2 text-xs"><input type="checkbox" name="is_featured" value="1" @checked($item->is_featured)> Featured</label>
                                <button type="submit" class="rounded bg-gray-800 px-3 py-1 text-xs text-white">Update</button>
                            </form>
                            <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600">Delete</button>
                            </form>
                        </details>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
