<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-kafe-100 bg-white/95 backdrop-blur-md shadow-sm shadow-kafe-100/50">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="group flex items-center gap-3">
            <img
                src="{{ asset(config('kafe.logo')) }}"
                alt="{{ config('kafe.name') }} logo"
                class="h-12 w-12 rounded-full object-cover shadow-sm ring-1 ring-kafe-200 transition group-hover:ring-kafe-400 sm:h-14 sm:w-14"
            >
            <div class="leading-tight hidden sm:block">
                <span class="font-display text-xl font-bold text-kafe-950 group-hover:text-kafe-700">The Kafé</span>
                <span class="block text-[10px] tracking-wider text-kafe-600">Jakhalabandha, Assam</span>
            </div>
        </a>

        <div class="hidden flex-1 items-center justify-center gap-8 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium hover:text-kafe-600 {{ request()->routeIs('home') ? 'text-kafe-600 border-b-2 border-kafe-600 pb-1' : 'text-kafe-800' }}">Home</a>
            <a href="{{ route('home') }}#about" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">About</a>
            <a href="{{ route('order.index') }}" class="text-sm font-medium text-kafe-800 hover:text-kafe-600 {{ request()->routeIs('order.*') ? 'text-kafe-600 border-b-2 border-kafe-600 pb-1' : '' }}">Menu</a>
            <a href="{{ route('home') }}#gallery" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">Gallery</a>
            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-kafe-800 hover:text-kafe-600">Contact</a>
        </div>

        <div class="hidden items-center gap-4 md:flex">
            <a href="{{ route('booking.create') }}" class="btn-primary flex items-center gap-2 rounded-full !bg-[#D6336C] hover:!bg-[#bd2a5e]">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Book a Party
            </a>
            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 text-sm font-semibold text-kafe-950 hover:text-[#D6336C]">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        Owner
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-sm font-semibold text-kafe-950 hover:text-[#D6336C]">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    Owner
                </a>
            @endauth
        </div>

        <button type="button" class="md:hidden rounded-lg p-2 text-kafe-800" @click="open = !open" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>

    <div x-show="open" x-cloak class="border-t border-kafe-100 bg-white px-4 py-4 md:hidden shadow-lg absolute w-full">
        <div class="flex flex-col gap-4">
            <a href="{{ route('home') }}" class="text-sm font-medium text-kafe-800">Home</a>
            <a href="{{ route('home') }}#about" class="text-sm font-medium text-kafe-800">About</a>
            <a href="{{ route('order.index') }}" class="text-sm font-medium text-kafe-800">Menu</a>
            <a href="{{ route('home') }}#gallery" class="text-sm font-medium text-kafe-800">Gallery</a>
            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-kafe-800">Contact</a>
            <hr class="border-kafe-100">
            <a href="{{ route('booking.create') }}" class="btn-primary text-center w-full !bg-[#D6336C]">Book a Party</a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-kafe-800 flex items-center gap-2">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                Owner login
            </a>
        </div>
    </div>
</header>
