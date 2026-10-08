@extends('layouts.kafe')

@section('title', config('kafe.name').' — Home')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-kafe-100 via-white to-kafe-50"></div>
        <div class="absolute -right-20 top-10 h-72 w-72 rounded-full bg-kafe-300/40 blur-3xl"></div>
        <div class="absolute -left-10 bottom-0 h-64 w-64 rounded-full bg-kafe-400/25 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-kafe-600">Brewing joy · Serving memories</p>
                <h1 class="mt-4 font-display text-4xl font-semibold leading-tight text-kafe-950 sm:text-5xl lg:text-6xl">
                    Pink-hued comfort,<br><span class="text-kafe-600">The Kafe</span> moments.
                </h1>
                <p class="mt-6 max-w-lg text-lg text-kafe-800">
                    Your neighbourhood stop in Jakhalabandha — masala chai, filter coffee, kachori, and homestyle thalis. Order for pickup or celebrate your next birthday with us.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('order.index') }}" class="btn-primary">Order now</a>
                    <a href="{{ route('booking.create') }}" class="btn-outline">Book a party</a>
                </div>
            </div>

            <div class="relative flex flex-col items-center">
                <div class="relative rounded-full bg-white p-6 shadow-2xl ring-4 ring-kafe-200">
                    <img
                        src="{{ asset(config('kafe.logo')) }}"
                        alt="{{ config('kafe.name') }}"
                        class="h-56 w-56 rounded-full object-cover sm:h-72 sm:w-72 lg:h-80 lg:w-80"
                    >
                </div>
                <div class="mt-8 max-w-xs rounded-2xl bg-white px-6 py-5 text-center shadow-xl ring-1 ring-kafe-100">
                    <p class="font-display text-lg font-semibold text-kafe-900">Open daily</p>
                    <p class="mt-1 text-sm text-kafe-600">Pure veg · Chai & coffee · Celebrations</p>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 class="section-title">Rooted in Jakhalabandha</h2>
                <p class="mt-4 text-kafe-800 leading-relaxed">
                    Inspired by the vibe of <a href="{{ config('kafe.instagram') }}" class="font-medium text-kafe-700 underline" target="_blank" rel="noopener">@the_kafe_jakhalabandha</a> — earthy tones, friendly service, and pure vegetarian comfort food for travellers and locals alike.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-kafe-800">
                    <li class="flex gap-2"><span class="text-kafe-600">✓</span> 100% vegetarian kitchen</li>
                    <li class="flex gap-2"><span class="text-kafe-600">✓</span> Party bookings for birthdays & gatherings</li>
                    <li class="flex gap-2"><span class="text-kafe-600">✓</span> WhatsApp order confirmation to the owner</li>
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <img src="https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=600&q=80" alt="Tea" class="h-48 w-full rounded-2xl object-cover shadow-md">
                <img src="https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=80" alt="Snacks" class="mt-8 h-48 w-full rounded-2xl object-cover shadow-md">
            </div>
        </div>
    </section>

    @if($featuredItems->isNotEmpty())
        <section class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="section-title">Guest favourites</h2>
                    <a href="{{ route('order.index') }}" class="text-sm font-semibold text-kafe-700 hover:text-kafe-900">View full menu →</a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredItems as $item)
                        <article class="rounded-2xl border border-kafe-100 bg-kafe-50/50 p-6 shadow-sm transition hover:shadow-md">
                            <p class="text-xs font-semibold uppercase tracking-wider text-kafe-600">{{ $item->category->name }}</p>
                            <h3 class="mt-2 font-display text-xl font-semibold text-kafe-950">{{ $item->name }}</h3>
                            @if($item->description)
                                <p class="mt-2 text-sm text-kafe-700 line-clamp-2">{{ $item->description }}</p>
                            @endif
                            <p class="mt-4 text-lg font-semibold text-kafe-800">{{ config('kafe.currency') }}{{ number_format($item->price, 0) }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="gallery" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Café mood</h2>
        <p class="mx-auto mt-3 max-w-2xl text-center text-kafe-700">Replace these placeholders with your Instagram photos anytime in the views or via a future media manager.</p>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                'photo-1509042239860-f550ce710b93',
                'photo-1511920170033-f8396924c348',
                'photo-1554118811-1e0d58224f24',
                'photo-1445110987605-c33326f9379d',
            ] as $id)
                <div class="aspect-square overflow-hidden rounded-2xl">
                    <img src="https://images.unsplash.com/{{ $id }}?auto=format&fit=crop&w=500&q=80" alt="Café gallery" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                </div>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl bg-gradient-to-r from-kafe-600 to-kafe-800 px-8 py-12 text-center text-white shadow-xl shadow-kafe-300/30 sm:px-16">
            <h2 class="font-display text-3xl font-semibold">Planning a birthday or family party?</h2>
            <p class="mx-auto mt-3 max-w-xl text-kafe-100">Pick your date, guest count, and occasion — we’ll receive your request on WhatsApp instantly.</p>
            <a href="{{ route('booking.create') }}" class="mt-8 inline-flex rounded-full bg-white px-8 py-3 text-sm font-bold text-kafe-700 hover:bg-kafe-50">Reserve your slot</a>
        </div>
    </section>
@endsection
