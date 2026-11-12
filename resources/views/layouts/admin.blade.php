<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Owner — '.config('kafe.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-kafe-50 font-sans text-gray-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 flex-shrink-0 bg-kafe-900 text-white md:block">
            <div class="border-b border-kafe-800 px-6 py-5">
                <img src="{{ asset(config('kafe.logo')) }}" alt="" class="mx-auto h-16 w-16 rounded-full object-cover ring-2 ring-kafe-400/40">
                <p class="mt-3 text-center font-display text-lg font-semibold">{{ config('kafe.name') }}</p>
                <p class="text-center text-xs text-kafe-300">Owner portal</p>
            </div>
            <nav class="space-y-1 p-4 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.dashboard') ? 'bg-kafe-800' : '' }}">Dashboard</a>
                @if(auth()->user()->canManageOrders())
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.orders.*') ? 'bg-kafe-800' : '' }}">Orders</a>
                @endif
                @if(auth()->user()->canManageBookings())
                    <a href="{{ route('admin.bookings.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.bookings.*') ? 'bg-kafe-800' : '' }}">Bookings</a>
                @endif
                @if(auth()->user()->canManageMenu())
                    <a href="{{ route('admin.menu.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.menu.*') ? 'bg-kafe-800' : '' }}">Menu items</a>
                    <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.categories.*') ? 'bg-kafe-800' : '' }}">Categories</a>
                    <a href="{{ route('admin.combos.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.combos.*') ? 'bg-kafe-800' : '' }}">Combos & offers</a>
                    <a href="{{ route('admin.offers.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.offers.*') ? 'bg-kafe-800' : '' }}">Discounts</a>
                    <a href="{{ route('admin.specials.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.specials.*') ? 'bg-kafe-800' : '' }}">Today’s specials</a>
                @endif
                @if(auth()->user()->canViewAccounting())
                    <a href="{{ route('admin.accounting.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.accounting.*') ? 'bg-kafe-800' : '' }}">Accounting</a>
                @endif
                @if(auth()->user()->hasAdminRole('owner'))
                    <a href="{{ route('admin.staff.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.staff.*') ? 'bg-kafe-800' : '' }}">Staff access</a>
                @endif
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800">View website</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left hover:bg-kafe-800">Log out</button>
                </form>
            </nav>
        </aside>

        <div class="flex-1">
            <header x-data="adminNotifications()" x-init="startPolling()" class="flex items-center justify-between border-b border-kafe-100 bg-white px-4 py-4 shadow-sm md:px-8">
                <h1 class="text-lg font-semibold text-kafe-900">@yield('heading', 'Dashboard')</h1>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.notifications.index') }}" class="relative rounded-md px-3 py-2 text-sm font-medium text-kafe-800 hover:bg-kafe-50">
                        Notifications
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="ml-1 rounded-full bg-rose-600 px-2 py-0.5 text-xs text-white"></span>
                    </a>
                    <button type="button" @click="toggleSound()" class="rounded-md border border-kafe-200 px-3 py-2 text-xs font-medium text-kafe-700" x-text="soundEnabled ? 'Sound on' : 'Sound off'"></button>
                </div>
            </header>
            <main class="p-4 md:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-kafe-100 px-4 py-3 text-sm text-kafe-900">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        function adminNotifications() {
            return {
                unreadCount: 0,
                seenIds: new Set(),
                initialized: false,
                soundEnabled: true,
                audioContext: null,
                pendingOrderType: null,
                startPolling() {
                    const enableAudio = () => {
                        window.removeEventListener('pointerdown', enableAudio);
                        window.removeEventListener('keydown', enableAudio);
                        this.unlockAudio();
                    };
                    window.addEventListener('pointerdown', enableAudio);
                    window.addEventListener('keydown', enableAudio);
                    this.poll();
                    window.setInterval(() => this.poll(), 10000);
                },
                async poll() {
                    try {
                        const response = await fetch(@js(route('admin.notifications.poll')), {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (!response.ok) {
                            console.error(`Notification polling failed with HTTP ${response.status}.`);
                            return;
                        }
                        const result = await response.json();
                        const currentIds = new Set(result.items.map(item => item.id));
                        const newItems = this.initialized
                            ? result.items.filter(item => !this.seenIds.has(item.id))
                            : [];
                        this.unreadCount = result.unread_count;
                        this.seenIds = currentIds;
                        if (newItems.length > 0 && this.soundEnabled) {
                            this.playAlert(newItems[0].order_type);
                        }
                        this.initialized = true;
                    } catch (error) {
                        console.error('Notification polling failed.', error);
                    }
                },
                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    if (!this.soundEnabled) {
                        this.pendingOrderType = null;
                        return;
                    }
                    const hasPendingAlert = this.pendingOrderType !== null;
                    this.unlockAudio();
                    if (!hasPendingAlert) this.playAlert('pickup');
                },
                unlockAudio() {
                    const AudioContextConstructor = window.AudioContext || window.webkitAudioContext;
                    if (!this.soundEnabled || !AudioContextConstructor) return;

                    this.audioContext ??= new AudioContextConstructor();
                    const context = this.audioContext;
                    if (context.state === 'running') {
                        this.playPendingAlert();
                        return;
                    }

                    context.resume()
                        .then(() => this.playPendingAlert())
                        .catch(error => console.error('Notification sound could not be played.', error));
                },
                playAlert(orderType = 'pickup') {
                    const AudioContextConstructor = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContextConstructor) return;

                    this.audioContext ??= new AudioContextConstructor();
                    if (this.audioContext.state !== 'running') {
                        this.pendingOrderType = orderType;
                        return;
                    }

                    this.playToneSequence(orderType);
                },
                playPendingAlert() {
                    if (!this.soundEnabled || this.pendingOrderType === null) return;

                    const orderType = this.pendingOrderType;
                    this.pendingOrderType = null;
                    this.playToneSequence(orderType);
                },
                playToneSequence(orderType) {
                    if (!this.soundEnabled) return;

                    const context = this.audioContext;
                    const sequences = {
                        pickup: [659, 784, 988],
                        dine_in: [523, 659, 784],
                        delivery: [988, 784, 659],
                    };
                    const notes = Object.prototype.hasOwnProperty.call(sequences, orderType)
                        ? sequences[orderType]
                        : [880, 1047];

                    notes.forEach((frequency, index) => {
                        const startAt = context.currentTime + index * 0.21;
                        const oscillator = context.createOscillator();
                        const gain = context.createGain();
                        oscillator.connect(gain);
                        gain.connect(context.destination);
                        oscillator.frequency.value = frequency;
                        gain.gain.setValueAtTime(0.001, startAt);
                        gain.gain.linearRampToValueAtTime(0.12, startAt + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.001, startAt + 0.16);
                        oscillator.start(startAt);
                        oscillator.stop(startAt + 0.16);
                    });
                },
            };
        }
    </script>
</body>
</html>
