@extends('layouts.kafe')

@section('title', 'Order — '.config('kafe.name'))

@section('content')
<div
    x-data="orderCart(
        @js($categories->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'items' => $category->availableItems->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'food_type' => $item->food_type,
                'price' => (string) $item->price,
                'discounted_price' => $item->discounted_price,
                'special_title' => $item->special_title,
                'image' => $item->imageUrl(),
                'variations' => $item->variations->where('is_available', true)->map(fn ($variation) => [
                    'id' => $variation->id,
                    'name' => $variation->name,
                    'price' => (string) $variation->price,
                    'discounted_price' => $variation->discounted_price,
                ])->values(),
                'addonGroups' => $item->addonGroups->where('is_active', true)->map(fn ($group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'min_select' => $group->min_select,
                    'max_select' => $group->max_select,
                    'is_required' => $group->is_required,
                    'options' => $group->options->where('is_available', true)->map(fn ($option) => [
                        'id' => $option->id,
                        'name' => $option->name,
                        'price_delta' => (string) $option->price_delta,
                    ])->values(),
                ])->values(),
            ])->values(),
        ])->values()),
        @js($combos->map(fn ($combo) => [
            'id' => $combo->id,
            'name' => $combo->name,
            'description' => $combo->description,
            'price' => (string) $combo->price,
            'groups' => $combo->groups->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'min_select' => $group->min_select,
                'max_select' => $group->max_select,
                'included_quantity' => $group->included_quantity,
                'items' => $group->eligibleItems->where('is_available', true)->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price_adjustment' => (string) $item->pivot->price_adjustment,
                ])->values(),
            ])->values(),
        ])->values())
    )"
    class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
>
    <div class="mb-10">
        <h1 class="section-title">Order for pickup</h1>
        <p class="mt-2 text-kafe-700">Choose your items, customize your order, and submit it to the café.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-10 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">
            <template x-for="category in categories" :key="category.id">
                <section>
                    <h2 class="font-display text-2xl font-semibold text-kafe-900" x-text="category.name"></h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <template x-for="item in category.items" :key="item.id">
                            <article x-data="{ variationId: null, addonIds: [] }" class="flex flex-col rounded-2xl border border-kafe-100 bg-white p-4 shadow-sm">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="mb-3 h-36 w-full rounded-xl object-cover">
                                </template>
                                <h3 class="font-semibold text-kafe-950" x-text="item.name"></h3>
                                <span class="mt-1 inline-flex w-fit rounded-full bg-kafe-50 px-2 py-1 text-xs text-kafe-700" x-text="item.food_type === 'veg' ? 'Vegetarian' : item.food_type === 'egg' ? 'Contains egg' : 'Chicken'"></span>
                                <span x-show="item.special_title" class="mt-1 inline-flex w-fit rounded-full bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700" x-text="item.special_title"></span>
                                <p class="mt-1 flex-1 text-sm text-kafe-600" x-text="item.description || ''"></p>

                                <div x-show="item.variations.length > 0" class="mt-3">
                                    <label class="text-xs font-medium text-kafe-700">Size / variation</label>
                                    <select x-model.number="variationId" class="mt-1 w-full rounded-lg border-kafe-200 text-sm">
                                        <option value="" x-text="`Regular · {{ config('kafe.currency') }}${Number(item.discounted_price).toFixed(2)}`"></option>
                                        <template x-for="variation in item.variations" :key="variation.id">
                                            <option :value="variation.id" x-text="`${variation.name} · {{ config('kafe.currency') }}${Number(variation.discounted_price).toFixed(2)}`"></option>
                                        </template>
                                    </select>
                                </div>

                                <template x-for="group in item.addonGroups" :key="group.id">
                                    <fieldset class="mt-3">
                                        <legend class="text-xs font-medium text-kafe-700">
                                            <span x-text="group.name"></span>
                                            <span x-show="group.is_required || group.min_select > 0" class="text-red-600"> · required</span>
                                        </legend>
                                        <template x-for="option in group.options" :key="option.id">
                                            <label class="mt-1 flex items-center gap-2 text-sm text-kafe-700">
                                                <input type="checkbox" x-model.number="addonIds" :value="option.id" class="rounded border-kafe-300 text-kafe-700">
                                                <span x-text="`${option.name} (+{{ config('kafe.currency') }}${Number(option.price_delta).toFixed(2)})`"></span>
                                            </label>
                                        </template>
                                    </fieldset>
                                </template>

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-lg font-bold text-kafe-800">
                                        <span x-show="Number(item.discounted_price) < Number(item.price)" class="mr-1 text-sm font-normal text-gray-500 line-through" x-text="`{{ config('kafe.currency') }}${Number(item.price).toFixed(2)}`"></span>
                                        {{ config('kafe.currency') }}<span x-text="Number(item.discounted_price).toFixed(2)"></span>
                                    </span>
                                    <button type="button" class="rounded-full bg-kafe-800 px-4 py-1.5 text-sm font-semibold text-white hover:bg-kafe-900 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!canAddItem(item, addonIds)" @click="addItem(item, variationId, addonIds)">
                                        Add
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>
            </template>

            <section x-show="combos.length > 0">
                <h2 class="font-display text-2xl font-semibold text-kafe-900">Combos & meals</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <template x-for="combo in combos" :key="combo.id">
                        <article x-data="comboBuilder(combo.groups)" class="rounded-2xl border border-kafe-100 bg-white p-4 shadow-sm">
                            <h3 class="font-semibold text-kafe-950" x-text="combo.name"></h3>
                            <p class="mt-1 text-sm text-kafe-600" x-text="combo.description || ''"></p>
                            <template x-for="group in combo.groups" :key="group.id">
                                <fieldset class="mt-3">
                                    <legend class="text-xs font-medium text-kafe-700">
                                        <span x-text="group.name"></span>
                                        <span class="text-kafe-500" x-text="` · choose ${group.min_select}–${group.max_select}`"></span>
                                        <span x-show="group.included_quantity > 1" class="text-kafe-500" x-text="` · includes ${group.included_quantity} of each choice`"></span>
                                    </legend>
                                    <template x-for="item in group.items" :key="item.id">
                                        <label class="mt-1 flex items-center gap-2 text-sm text-kafe-700">
                                            <input type="checkbox" x-model.number="selected[group.id]" :value="item.id" class="rounded border-kafe-300 text-kafe-700">
                                            <span x-text="item.name"></span>
                                            <span x-show="Number(item.price_adjustment) !== 0" class="text-xs text-kafe-500" x-text="`(${item.price_adjustment > 0 ? '+' : ''}{{ config('kafe.currency') }}${Number(item.price_adjustment).toFixed(2)})`"></span>
                                        </label>
                                    </template>
                                </fieldset>
                            </template>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="font-bold text-kafe-800">{{ config('kafe.currency') }}<span x-text="Number(combo.price).toFixed(2)"></span></span>
                                <button type="button" class="rounded-full bg-kafe-800 px-4 py-1.5 text-sm font-semibold text-white hover:bg-kafe-900 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!canAdd" @click="addCombo(combo, selected)">
                                    Add combo
                                </button>
                            </div>
                        </article>
                    </template>
                </div>
            </section>

            @if($categories->isEmpty() && $combos->isEmpty())
                <p class="text-kafe-700">Menu is being updated. Please check back soon.</p>
            @endif
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-2xl border border-kafe-200 bg-white p-6 shadow-lg">
                <h2 class="font-display text-xl font-semibold">Your cart</h2>

                <template x-if="cart.length === 0">
                    <p class="mt-4 text-sm text-kafe-600">No items yet. Browse the menu and tap Add.</p>
                </template>

                <ul class="mt-4 space-y-3" x-show="cart.length > 0">
                    <template x-for="line in cart" :key="line.key">
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span>
                                <span class="font-medium" x-text="line.name"></span>
                                <span x-show="line.details" class="block text-xs text-gray-500" x-text="line.details"></span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" class="h-7 w-7 rounded-full bg-kafe-100 text-kafe-800" @click="changeQty(line.key, -1)">−</button>
                                <span x-text="line.qty"></span>
                                <button type="button" class="h-7 w-7 rounded-full bg-kafe-100 text-kafe-800" @click="changeQty(line.key, 1)">+</button>
                            </div>
                        </li>
                    </template>
                </ul>

                <p class="mt-4 border-t border-kafe-100 pt-4 text-lg font-bold" x-show="cart.length > 0">
                    Total: {{ config('kafe.currency') }}<span x-text="total.toFixed(2)"></span>
                </p>

                <form method="POST" action="{{ route('order.store') }}" class="mt-6 space-y-4" @submit="prepareForm">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', \Illuminate\Support\Str::random(64)) }}">
                    <div id="order-items-container"></div>
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-kafe-800">Your name</label>
                        <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-kafe-800">Mobile number</label>
                        <input id="customer_phone" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="20" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500" placeholder="10-digit mobile">
                    </div>
                    <div>
                        <label for="customer_note" class="block text-sm font-medium text-kafe-800">Note (optional)</label>
                        <textarea id="customer_note" name="customer_note" rows="2" maxlength="500" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">{{ old('customer_note') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full" :disabled="cart.length === 0">
                        Submit order
                    </button>
                    <p class="text-center text-xs text-kafe-500">Payment is made directly at the café. No online payment is required.</p>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
function orderCart(categories, combos) {
    return {
        categories,
        combos,
        cart: [],
        get total() {
            return this.cart.reduce((sum, line) => sum + line.price * line.qty, 0);
        },
        addItem(item, variationId, addonIds) {
            const variation = item.variations.find(candidate => Number(candidate.id) === Number(variationId));
            const addonOptions = item.addonGroups.flatMap(group => group.options).filter(option => addonIds.includes(Number(option.id)));
            const price = Number(variation?.discounted_price ?? item.discounted_price) + addonOptions.reduce((sum, option) => sum + Number(option.price_delta), 0);
            const key = `item-${item.id}-${variation?.id ?? 'base'}-${[...addonIds].sort((a, b) => a - b).join(',')}`;
            const existing = this.cart.find(line => line.key === key);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    key,
                    name: item.name + (variation ? ` · ${variation.name}` : ''),
                    price,
                    qty: 1,
                    menuItemId: item.id,
                    variationId: variation?.id ?? null,
                    addonIds: [...addonIds],
                });
            }
        },
        canAddItem(item, addonIds) {
            return item.addonGroups.every(group => {
                const count = group.options.filter(option => addonIds.includes(Number(option.id))).length;
                const minimum = Math.max(group.min_select, group.is_required ? 1 : 0);
                return count >= minimum && count <= group.max_select;
            });
        },
        addCombo(combo, selected) {
            const selections = Object.fromEntries(
                Object.entries(selected).map(([groupId, itemIds]) => [groupId, [...itemIds]])
            );
            const selectedItems = combo.groups.flatMap(group => group.items
                .filter(item => selections[group.id]?.includes(Number(item.id)))
                .map(item => ({ item, group }))
            );
            const details = selectedItems.flatMap(selection =>
                Array(selection.group.included_quantity).fill(selection.item.name)
            ).join(', ');
            const price = Number(combo.price) + selectedItems.reduce(
                (sum, selection) => sum + Number(selection.item.price_adjustment) * selection.group.included_quantity,
                0
            );
            const key = `combo-${combo.id}-${JSON.stringify(selections)}`;
            const existing = this.cart.find(line => line.key === key);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    key,
                    name: combo.name,
                    details,
                    price,
                    qty: 1,
                    comboId: combo.id,
                    comboSelections: selections,
                });
            }
        },
        changeQty(key, delta) {
            const line = this.cart.find(candidate => candidate.key === key);
            if (!line) return;
            line.qty += delta;
            if (line.qty <= 0) this.cart = this.cart.filter(candidate => candidate.key !== key);
        },
        prepareForm(event) {
            if (this.cart.length === 0) {
                event.preventDefault();
                return;
            }
            const container = document.getElementById('order-items-container');
            container.replaceChildren();
            const addInput = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                container.appendChild(input);
            };
            this.cart.forEach((line, index) => {
                addInput(`items[${index}][quantity]`, line.qty);
                if (line.menuItemId) {
                    addInput(`items[${index}][menu_item_id]`, line.menuItemId);
                    if (line.variationId) addInput(`items[${index}][menu_variation_id]`, line.variationId);
                    line.addonIds.forEach((id, optionIndex) => addInput(`items[${index}][addon_option_ids][${optionIndex}]`, id));
                }
                if (line.comboId) {
                    addInput(`items[${index}][combo_id]`, line.comboId);
                    Object.entries(line.comboSelections).forEach(([groupId, itemIds]) => {
                        itemIds.forEach((id, itemIndex) => addInput(`items[${index}][combo_selections][${groupId}][${itemIndex}]`, id));
                    });
                }
            });
        },
    };
}

function comboBuilder(groups) {
    return {
        selected: Object.fromEntries(groups.map(group => [group.id, []])),
        get canAdd() {
            return groups.every(group => {
                const count = this.selected[group.id]?.length ?? 0;
                return count >= group.min_select && count <= group.max_select;
            });
        },
    };
}
</script>
@endpush
