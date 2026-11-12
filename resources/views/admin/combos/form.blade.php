@extends('layouts.admin')

@section('heading', $combo->exists ? 'Edit '.$combo->name : 'Create combo')

@section('content')
<form method="POST" action="{{ $combo->exists ? route('admin.combos.update', $combo) : route('admin.combos.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($combo->exists) @method('PUT') @endif
    <section class="grid gap-4 rounded-xl bg-white p-5 shadow-sm sm:grid-cols-2">
        <label class="text-sm font-medium">Name<input name="name" required maxlength="160" value="{{ old('name', $combo->name) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Type<select name="kind" required class="mt-1 w-full rounded-md border-gray-300"><option value="meal" @selected(old('kind', $combo->kind ?: 'meal') === 'meal')>Meal</option><option value="offer" @selected(old('kind', $combo->kind) === 'offer')>Combo offer</option></select></label>
        <label class="text-sm font-medium">Price ({{ config('kafe.currency') }})<input name="price" type="number" min="0.01" step="0.01" required value="{{ old('price', $combo->price) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Sort order<input name="sort_order" type="number" min="0" value="{{ old('sort_order', $combo->sort_order ?? 0) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Starts at<input name="starts_at" type="datetime-local" value="{{ old('starts_at', $combo->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium">Ends at<input name="ends_at" type="datetime-local" value="{{ old('ends_at', $combo->ends_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300"></label>
        <label class="text-sm font-medium sm:col-span-2">Description<textarea name="description" rows="2" maxlength="1000" class="mt-1 w-full rounded-md border-gray-300">{{ old('description', $combo->description) }}</textarea></label>
        <label class="text-sm font-medium">Combo image<input name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full text-sm"></label>
        <label class="flex items-center gap-2 pt-6 text-sm"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $combo->exists ? $combo->is_active : true))> Active</label>
    </section>

    <section x-data="{ groups: @js($combo->exists
        ? $combo->groups->map(fn ($group) => [
            'name' => $group->name,
            'min_select' => $group->min_select,
            'max_select' => $group->max_select,
            'included_quantity' => $group->included_quantity,
            'items' => $group->eligibleItems->map(fn ($item) => [
                'menu_item_id' => $item->id,
                'price_adjustment' => (string) $item->pivot->price_adjustment,
            ])->values(),
        ])->values()
        : collect([['name' => '', 'min_select' => 1, 'max_select' => 1, 'included_quantity' => 1, 'items' => [['menu_item_id' => '', 'price_adjustment' => '0.00']]]])
    ) }" class="rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div><h2 class="font-semibold">Builder groups</h2><p class="text-sm text-gray-500">Customers choose eligible items from each group. Price adjustments are per included item.</p></div>
            <button type="button" @click="groups.push({name: '', min_select: 1, max_select: 1, included_quantity: 1, items: [{menu_item_id: '', price_adjustment: '0.00'}]})" class="rounded-md border px-3 py-2 text-sm">Add group</button>
        </div>
        <div class="mt-4 space-y-4">
            <template x-for="(group, groupIndex) in groups" :key="groupIndex">
                <fieldset class="rounded-lg border border-gray-200 p-4">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="text-xs font-medium">Group name<input :name="`groups[${groupIndex}][name]`" x-model="group.name" required maxlength="100" class="mt-1 w-full rounded-md border-gray-300 text-sm"></label>
                        <label class="text-xs font-medium">Minimum selections<input :name="`groups[${groupIndex}][min_select]`" x-model.number="group.min_select" type="number" min="0" max="20" required class="mt-1 w-full rounded-md border-gray-300 text-sm"></label>
                        <label class="text-xs font-medium">Maximum selections<input :name="`groups[${groupIndex}][max_select]`" x-model.number="group.max_select" type="number" min="1" max="20" required class="mt-1 w-full rounded-md border-gray-300 text-sm"></label>
                        <label class="text-xs font-medium">Quantity per selection<input :name="`groups[${groupIndex}][included_quantity]`" x-model.number="group.included_quantity" type="number" min="1" max="20" required class="mt-1 w-full rounded-md border-gray-300 text-sm"></label>
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-for="(item, itemIndex) in group.items" :key="itemIndex">
                            <div class="grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
                                <select :name="`groups[${groupIndex}][items][${itemIndex}][menu_item_id]`" x-model.number="item.menu_item_id" required class="rounded-md border-gray-300 text-sm">
                                    <option value="">Select eligible menu item</option>
                                    @foreach($menuItems as $menuItem)
                                        <option value="{{ $menuItem->id }}">{{ $menuItem->name }} · {{ config('kafe.currency') }}{{ number_format((float) $menuItem->price, 2) }}</option>
                                    @endforeach
                                </select>
                                <input :name="`groups[${groupIndex}][items][${itemIndex}][price_adjustment]`" x-model="item.price_adjustment" type="number" step="0.01" required placeholder="Price adjustment" class="rounded-md border-gray-300 text-sm">
                                <button type="button" @click="group.items.splice(itemIndex, 1)" class="text-sm text-red-600">Remove</button>
                            </div>
                        </template>
                        <button type="button" @click="group.items.push({menu_item_id: '', price_adjustment: '0.00'})" class="text-xs font-medium text-kafe-800">+ Add eligible item</button>
                    </div>
                    <button type="button" @click="groups.splice(groupIndex, 1)" class="mt-3 text-xs text-red-600">Remove group</button>
                </fieldset>
            </template>
        </div>
    </section>

    <div class="flex justify-between">
        <a href="{{ route('admin.combos.index') }}" class="text-sm text-kafe-800 underline">Back to combos</a>
        <button class="rounded-md bg-kafe-800 px-5 py-2 font-semibold text-white">Save combo</button>
    </div>
</form>
@endsection
