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
            <input type="number" min="0" max="99999999.99" step="0.01" name="price" required class="mt-1 w-full rounded-md border-gray-300">
        </div>
        <div>
            <label class="text-sm font-medium">Food type</label>
            <select name="food_type" required class="mt-1 w-full rounded-md border-gray-300">
                <option value="veg">Vegetarian</option>
                <option value="egg">Contains egg</option>
                <option value="chicken">Chicken</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Sort order</label>
            <input type="number" name="sort_order" value="0" class="mt-1 w-full rounded-md border-gray-300">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Description</label>
            <textarea name="description" rows="2" class="mt-1 w-full rounded-md border-gray-300"></textarea>
        </div>
        <div x-data="{ photoName: null, photoPreview: null }">
            <label class="text-sm font-medium">Photo</label>
            <div class="mt-1 flex items-center gap-4">
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-sm"
                       x-ref="photo"
                       x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL($refs.photo.files[0]);
                       ">
                <template x-if="photoPreview">
                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                        <img :src="photoPreview" class="h-full w-full object-cover">
                    </div>
                </template>
            </div>
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
                    <td class="px-4 py-3 flex items-center gap-3">
                        @if($item->image_path)
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" class="h-12 w-12 flex-shrink-0 rounded-lg object-cover shadow-sm">
                        @else
                            <div class="h-12 w-12 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</p>
                        </div>
                    </td>
                    <td class="py-3">{{ $item->category->name }}</td>
                    <td class="py-3">{{ config('kafe.currency') }}{{ number_format($item->price, 2) }}</td>
                    <td class="py-3 text-xs">
                        @if($item->is_available)<span class="text-emerald-600">Available</span>@else<span class="text-red-600">Hidden</span>@endif
                        · {{ ['veg' => 'Vegetarian', 'egg' => 'Contains egg', 'chicken' => 'Chicken'][$item->food_type] ?? 'Other' }}
                        @if($item->is_featured) · Featured @endif
                    </td>
                    <td class="px-4 py-3">
                        <details class="text-sm">
                            <summary class="cursor-pointer font-medium text-kafe-800">Edit</summary>
                            <a href="{{ route('admin.menu.customize.edit', $item) }}" class="mt-2 inline-block text-xs font-medium text-kafe-800 underline">Variations, add-ons, hours & images</a>
                            <p class="mt-1 text-xs text-gray-500">{{ $item->variations_count }} variations · {{ $item->addon_groups_count }} add-on groups · {{ $item->availability_schedules_count }} time windows</p>
                            <form method="POST" action="{{ route('admin.menu.update', $item) }}" enctype="multipart/form-data" class="mt-2 space-y-2 rounded border border-gray-200 p-3">
                                @csrf
                                @method('PUT')
                                <select name="menu_category_id" class="w-full rounded-md border-gray-300 text-sm">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected($cat->id === $item->menu_category_id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="name" value="{{ $item->name }}" class="w-full rounded-md border-gray-300 text-sm">
                                <input type="number" min="0" max="99999999.99" step="0.01" name="price" value="{{ $item->price }}" class="w-full rounded-md border-gray-300 text-sm">
                                <select name="food_type" class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="veg" @selected($item->food_type === 'veg')>Vegetarian</option>
                                    <option value="egg" @selected($item->food_type === 'egg')>Contains egg</option>
                                    <option value="chicken" @selected($item->food_type === 'chicken')>Chicken</option>
                                </select>
                                <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ $item->description }}</textarea>
                                <div x-data="{ photoName: null, photoPreview: '{{ $item->imageUrl() }}' }">
                                    <div class="flex items-center gap-3">
                                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-xs"
                                               x-ref="photo"
                                               x-on:change="
                                                    photoName = $refs.photo.files[0].name;
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                                    reader.readAsDataURL($refs.photo.files[0]);
                                               ">
                                        <template x-if="photoPreview">
                                            <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded border border-gray-200">
                                                <img :src="photoPreview" class="h-full w-full object-cover">
                                            </div>
                                        </template>
                                    </div>
                                </div>
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
