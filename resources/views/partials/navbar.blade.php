<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-kafe-100 bg-white/95 backdrop-blur-md shadow-sm shadow-kafe-100/50">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-3">
            <img
                src="{{ asset(config('kafe.logo')) }}"
                alt="{{ config('kafe.name') }} logo"
                class="h-14 w-14 rounded-full object-cover ring-2 ring-kafe-200 transition group-hover:ring-kafe-400 sm:h-16 sm:w-16"
            >
            <div class="leading-tight hidden sm:block">
                <span class="font-display text-xl font-semibold text-kafe-900 group-hover:text-kafe-700">{{ config('kafe.name') }}</span>
                <span class="block text-xs text-kafe-500">{{ config('kafe.location') }}</span>
            </div>
        </a>

        <div class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-kafe-800 hover:text-kafe-600 {{ request()->routeIs('home') ? 'text-kafe-600' : '' }}">Home</a>
            <a href="{{ route('home') }}#about" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">About</a>
            <a href="{{ route('order.index') }}" class="text-sm font-medium text-kafe-800 hover:text-kafe-600 {{ request()->routeIs('order.*') ? 'text-kafe-600' : '' }}">Order</a>
            <a href="{{ route('home') }}#gallery" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">Gallery</a>
            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">Contact</a>
            <a href="{{ route('booking.create') }}" class="btn-primary !py-2 !px-5">Book a Party</a>
            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-kafe-700 hover:text-kafe-900">Owner</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-kafe-600 hover:text-kafe-800">Owner login</a>
            @endauth
        </div>

        <button type="button" class="md:hidden rounded-lg p-2 text-kafe-800" @click="open = !open" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>

    <div x-show="open" x-cloak class="border-t border-kafe-100 bg-white px-4 py-4 md:hidden">
        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}" class="text-sm font-medium text-kafe-800">Home</a>
            <a href="{{ route('order.index') }}" class="text-sm font-medium text-kafe-800">Order</a>
            <a href="{{ route('booking.create') }}" class="btn-primary text-center">Book a Party</a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-kafe-800">Owner login</a>
        </div>
    </div>
</header>
