@extends('layouts.kafe')

@section('title', 'Order — '.config('kafe.name'))

@section('content')
<div
    x-data="orderCart(@js($categories->map(fn ($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'items' => $c->availableItems->map(fn ($i) => [
            'id' => $i->id,
            'name' => $i->name,
            'description' => $i->description,
            'price' => (float) $i->price,
            'image' => $i->imageUrl(),
        ]),
    ])))"
    class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
>
    <div class="mb-10">
        <h1 class="section-title">Order for pickup</h1>
        <p class="mt-2 text-kafe-700">Add items to your cart, then send the order to the owner on WhatsApp.</p>
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
        <div class="lg:col-span-2 space-y-10">
            <template x-for="category in categories" :key="category.id">
                <section>
                    <h2 class="font-display text-2xl font-semibold text-kafe-900" x-text="category.name"></h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <template x-for="item in category.items" :key="item.id">
                            <article class="flex flex-col rounded-2xl border border-kafe-100 bg-white p-4 shadow-sm">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="mb-3 h-36 w-full rounded-xl object-cover">
                                </template>
                                <h3 class="font-semibold text-kafe-950" x-text="item.name"></h3>
                                <p class="mt-1 flex-1 text-sm text-kafe-600" x-text="item.description || ''"></p>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-lg font-bold text-kafe-800">{{ config('kafe.currency') }}<span x-text="item.price.toFixed(0)"></span></span>
                                    <button type="button" class="rounded-full bg-kafe-800 px-4 py-1.5 text-sm font-semibold text-white hover:bg-kafe-900" @click="addItem(item)">
                                        Add
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>
            </template>

            @if($categories->isEmpty())
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
                    <template x-for="line in cart" :key="line.id">
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <span class="font-medium" x-text="line.name"></span>
                            <div class="flex items-center gap-2">
                                <button type="button" class="h-7 w-7 rounded-full bg-kafe-100 text-kafe-800" @click="changeQty(line.id, -1)">−</button>
                                <span x-text="line.qty"></span>
                                <button type="button" class="h-7 w-7 rounded-full bg-kafe-100 text-kafe-800" @click="changeQty(line.id, 1)">+</button>
                            </div>
                        </li>
                    </template>
                </ul>

                <p class="mt-4 border-t border-kafe-100 pt-4 text-lg font-bold" x-show="cart.length > 0">
                    Total: {{ config('kafe.currency') }}<span x-text="total.toFixed(2)"></span>
                </p>

                <form method="POST" action="{{ route('order.store') }}" class="mt-6 space-y-4" @submit="prepareForm">
                    @csrf
                    <div id="order-items-container"></div>

                    <div>
                        <label class="block text-sm font-medium text-kafe-800">Your name</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-kafe-800">WhatsApp / phone</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500" placeholder="10-digit mobile">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-kafe-800">Note (optional)</label>
                        <textarea name="customer_note" rows="2" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">{{ old('customer_note') }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full" :disabled="cart.length === 0">
                        Place order on WhatsApp
                    </button>
                    <p class="text-xs text-kafe-500">You’ll be redirected to WhatsApp to send the order to the café owner.</p>
                    <p class="text-center text-xs text-kafe-600">
                        Or
                        <a href="{{ app(\App\Services\WhatsAppMessageBuilder::class)->directChatUrl() }}" target="_blank" rel="noopener" class="font-semibold text-kafe-700 underline hover:text-kafe-900">message the owner directly on WhatsApp</a>
                        with your own text.
                    </p>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
function orderCart(categories) {
    return {
        categories,
        cart: [],
        get total() {
            return this.cart.reduce((sum, line) => sum + line.price * line.qty, 0);
        },
        addItem(item) {
            const existing = this.cart.find(l => l.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ id: item.id, name: item.name, price: item.price, qty: 1 });
            }
        },
        changeQty(id, delta) {
            const line = this.cart.find(l => l.id === id);
            if (!line) return;
            line.qty += delta;
            if (line.qty <= 0) {
                this.cart = this.cart.filter(l => l.id !== id);
            }
        },
        prepareForm(e) {
            if (this.cart.length === 0) {
                e.preventDefault();
                return;
            }
            const container = document.getElementById('order-items-container');
            container.innerHTML = '';
            this.cart.forEach((line, index) => {
                container.insertAdjacentHTML('beforeend',
                    `<input type="hidden" name="items[${index}][menu_item_id]" value="${line.id}">` +
                    `<input type="hidden" name="items[${index}][quantity]" value="${line.qty}">`
                );
            });
        },
    };
}
</script>
@endpush
