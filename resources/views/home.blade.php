@extends('layouts.kafe')

@section('title', config('kafe.name').' — Home')

@section('content')
    <section class="relative w-full overflow-hidden bg-[#fff5f8]">
        <!-- The background image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero-banner.png') }}" class="h-full w-full object-cover object-center" alt="Hero background">
        </div>

        <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 px-4 pb-20 pt-12 sm:px-6 lg:grid-cols-[1.2fr_1fr] lg:gap-8 lg:px-8 lg:pb-32 lg:pt-20">
            <!-- Left Side Text & Icons -->
            <div class="max-w-[38rem]">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#D6336C]">Brewing joy · Serving memories</p>
                <h1 class="mt-4 font-display text-5xl font-bold leading-[1.1] text-[#3D1B28] sm:text-6xl lg:text-[4.5rem]">
                    Pink-hued comfort,<br><span class="text-[#D6336C]">The Kafé</span> moments.
                </h1>
                <p class="mt-6 text-[15px] leading-relaxed text-[#3D1B28]">
                    Your neighbourhood stop in <strong>Jakhalabandha</strong> — masala chai, <strong>filter coffee</strong>, <strong>kachori</strong>, and <strong>homestyle thalis</strong>. Order for pickup or celebrate your next birthday with us.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('order.index') }}" class="btn-primary flex items-center gap-2 rounded-full !bg-[#D6336C] px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#D6336C]/30 hover:!bg-[#bd2a5e]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Order now
                    </a>
                    <a href="{{ route('booking.create') }}" class="btn-outline flex items-center gap-2 rounded-full !border-[1.5px] !border-[#D6336C] !text-[#D6336C] px-8 py-3.5 text-sm font-semibold transition hover:!bg-[#fff0f5]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Book a party
                    </a>
                </div>

                <div class="mt-14 flex flex-wrap items-center gap-4 sm:gap-7">
                    @foreach([
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>', 'label' => 'Veg & Non-Veg'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>', 'label' => 'Freshly Prepared'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>', 'label' => 'Homely Taste'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>', 'label' => 'Party Bookings'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>', 'label' => 'Local Favourite']
                    ] as $badge)
                        <div class="flex flex-col items-center gap-2.5 text-center">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-[#D6336C] shadow-[0_4px_10px_rgba(214,51,108,0.1)] ring-1 ring-[#ffe8f2]">
                                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $badge['icon'] !!}</svg>
                            </div>
                            <span class="text-[10px] font-bold text-[#3D1B28]">{{ $badge['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Side Overlay -->
            <div class="relative mt-12 flex h-full flex-col items-center justify-start lg:mt-0">

                <!-- The stamp "Brewing Joy / The Kafe" -->
                <div class="relative z-10 flex h-60 w-60 items-center justify-center rounded-full bg-white shadow-2xl drop-shadow-xl lg:-ml-32 lg:h-72 lg:w-72">
                    <img src="{{ asset('images/logo-pink.png') }}" class="h-full w-full rounded-full object-cover">
                </div>

                <!-- Open daily widget -->
                <div class="relative z-10 mt-8 flex flex-col items-center rounded-xl bg-white/95 px-8 py-4 shadow-xl backdrop-blur-md lg:-ml-32 lg:-mt-6">
                    <div class="flex items-center gap-2 text-[#3D1B28]">
                        <svg class="h-[18px] w-[18px] text-[#D6336C]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-display text-[15px] font-bold">Find us in Jakhalabandha</p>
                    </div>
                    <p class="mt-1 text-[11px] tracking-wide text-[#a01558]">Chai & coffee - Snacks - Celebrations</p>
                </div>

                <!-- "Good Food Brighter Days" text -->
                <div class="absolute right-0 top-0 rotate-12 font-cursive text-[2.5rem] leading-[1.1] text-[#713144] lg:-right-4 lg:-top-4 lg:text-[3.25rem]">
                    Good<br>Food<br>Brighter<br>Days <span class="text-[#a01558]">♥</span>
                </div>

            </div>
        </div>
    </section>

    <section id="about" class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
            <div class="relative z-10">
                <h2 class="section-title">Rooted in Jakhalabandha</h2>
                <p class="mt-6 text-[15px] leading-relaxed text-kafe-800">
                    Inspired by the vibe of <a href="{{ config('kafe.instagram') }}" class="font-medium text-[#D6336C]" target="_blank" rel="noopener">@the_kafe_jakhalabandha</a> — earthy tones, friendly service, and café favourites for travellers and locals alike.
                </p>
                <ul class="mt-8 space-y-4 text-[15px] text-kafe-800">
                    <li class="flex items-center gap-3">
                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D6336C] text-white"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                        Vegetarian & non-vegetarian favourites
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D6336C] text-white"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                        Party bookings for birthdays & gatherings
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D6336C] text-white"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                        Direct order submission to the café
                    </li>
                </ul>
                <div class="mt-10">
                    <a href="{{ route('home') }}#about" class="btn-primary !bg-[#D6336C] hover:!bg-[#bd2a5e]">Know more about us &rarr;</a>
                </div>
            </div>

            <div class="relative flex justify-end pb-10 pr-6">
                <!-- Decorative background shapes -->
                <div class="absolute -bottom-10 right-10 h-72 w-80 rounded-[3rem] bg-gradient-to-tr from-[#ffe8f2] to-transparent"></div>
                <svg class="absolute -right-4 top-10 h-32 w-32 text-kafe-200 opacity-60" fill="currentColor" viewBox="0 0 100 100"><path d="M50 0C50 27.614 27.614 50 0 50c27.614 0 50 22.386 50 50 0-27.614 22.386-50 50-50-27.614 0-50-22.386-50-50z"/></svg>

                <div class="relative z-10 flex w-full max-w-md items-end gap-4">
                    <img src="https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=600&q=80" alt="Tea" class="h-64 w-48 rounded-3xl border-8 border-white object-cover shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=80" alt="Snacks" class="h-48 w-56 -ml-16 mb-8 rounded-3xl border-8 border-white object-cover shadow-2xl">
                </div>

                <div class="absolute bottom-0 right-16 -rotate-6 font-cursive text-[2rem] leading-tight text-[#b88c83]">
                    Chai<br>Conversations<br>& Good Food<br>Always <span class="text-[#b88c83]">♥</span>
                </div>
            </div>
        </div>
    </section>

    @if($featuredItems->isNotEmpty())
        <section class="relative overflow-hidden bg-white py-24">
            <!-- Decorative background elements -->
            <div class="absolute right-10 top-1/2 hidden -translate-y-1/2 rotate-[-5deg] font-cursive text-[3.5rem] leading-tight text-[#e8c6c0] opacity-70 lg:block">
                <span class="pl-8">Simple</span><br>
                <span class="pl-4">Food</span><br>
                <span class="pl-12">Happy</span><br>
                <span class="pl-16">People</span> <span class="text-[#e8c6c0]">♥</span>
            </div>
            <div class="absolute -right-10 top-20 h-40 w-40 rounded-full border-[1px] border-[#ffe8f2] opacity-50"></div>
            <div class="absolute -right-20 top-40 h-64 w-64 rounded-full border-[1px] border-[#ffe8f2] opacity-50"></div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4 border-b border-kafe-100 pb-4">
                    <h2 class="section-title relative inline-block">
                        Guest favourites
                        <div class="absolute -bottom-4 left-0 h-[2px] w-12 bg-[#D6336C]"></div>
                    </h2>
                    <a href="{{ route('order.index') }}" class="text-sm font-semibold text-[#D6336C] hover:text-[#bd2a5e]">View full menu &rarr;</a>
                </div>
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 lg:pr-40">
                    @foreach($featuredItems as $item)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-[#ffe8f2] bg-white shadow-sm transition hover:shadow-md">
                            @if($item->image_path)
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" class="h-48 w-full object-cover">
                            @endif
                            <div class="flex flex-1 flex-col p-6">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#D6336C]">{{ $item->category->name }}</p>
                                <h3 class="mt-1 font-display text-2xl font-bold text-kafe-950">{{ $item->name }}</h3>
                                <p class="mt-1 text-xs text-kafe-600">{{ ['veg' => 'Vegetarian', 'egg' => 'Contains egg', 'chicken' => 'Chicken'][$item->food_type] ?? 'Other' }}</p>
                                @if($item->description)
                                    <p class="mt-1 text-sm text-kafe-600 line-clamp-2">{{ $item->description }}</p>
                                @endif
                                <div class="mt-auto flex items-center justify-between pt-6">
                                    <p class="text-xl font-bold text-[#D6336C]">{{ config('kafe.currency') }}{{ number_format($item->price, 0) }}</p>
                                    <a href="{{ route('order.index') }}" class="btn-primary !bg-[#D6336C] !px-5 !py-1.5 !text-[13px] hover:!bg-[#bd2a5e]">Order now</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="gallery" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="section-title">Café mood</h2>
                <div class="mt-2 flex items-center gap-3">
                    <div class="h-[2px] w-8 bg-[#D6336C]"></div>
                    <p class="text-[13px] text-kafe-600">A glimpse of good food, warm conversations and beautiful moments at The Kafé.</p>
                </div>
            </div>
            <a href="{{ route('home') }}#gallery" class="text-[13px] font-semibold text-[#D6336C] hover:text-[#bd2a5e]">View gallery &rarr;</a>
        </div>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="aspect-[4/3] overflow-hidden rounded-2xl">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=500&q=80" alt="Café gallery" class="h-full w-full object-cover transition duration-500 hover:scale-105">
            </div>
            <div class="aspect-[4/3] overflow-hidden rounded-2xl">
                <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=500&q=80" alt="Café gallery" class="h-full w-full object-cover transition duration-500 hover:scale-105">
            </div>
            <div class="aspect-[4/3] overflow-hidden rounded-2xl">
                <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=500&q=80" alt="Café gallery" class="h-full w-full object-cover transition duration-500 hover:scale-105">
            </div>
            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl">
                <img src="https://images.unsplash.com/photo-1445110987605-c33326f9379d?auto=format&fit=crop&w=500&q=80" alt="Café gallery" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                <div class="absolute inset-0 bg-kafe-900/10"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="font-cursive text-3xl leading-tight text-[#ffebbd] text-center shadow-sm drop-shadow-md">
                        Good<br>Food<br>Good<br>Mood
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-[#D6336C] to-[#8a1c43] text-white shadow-2xl">
            <div class="grid items-center gap-8 lg:grid-cols-3 lg:gap-12">
                <!-- Left: Image -->
                <div class="relative h-64 lg:h-full">
                    <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80" alt="Birthday Cake" class="absolute inset-0 h-full w-full object-cover">
                    <!-- overlay gradient to blend image -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#D6336C] lg:bg-gradient-to-r"></div>
                </div>

                <!-- Center: Text and button -->
                <div class="flex flex-col items-center p-8 text-center lg:py-16">
                    <h2 class="font-display text-3xl font-bold lg:text-4xl">Planning a birthday or family party?</h2>
                    <p class="mt-3 text-[13px] text-white/90">Pick your date, guest count, and occasion — our team will receive your request.</p>
                    <a href="{{ route('booking.create') }}" class="mt-8 flex items-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-bold text-[#D6336C] transition hover:bg-kafe-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Reserve your slot
                    </a>
                </div>

                <!-- Right: Features -->
                <div class="flex flex-col gap-5 p-8 pl-0 lg:py-16 lg:pr-12">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/></svg>
                        <span class="text-sm font-medium">Birthday parties</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm font-medium">Family gatherings</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Custom arrangements</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Hassle-free booking</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
