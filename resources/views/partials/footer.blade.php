<footer id="contact" class="mt-20 border-t border-kafe-200 bg-[#6b0e30] text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.5fr_1fr_1fr_1fr] lg:gap-8 lg:px-8">
        <!-- Logo and Branding -->
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center lg:items-start lg:flex-col lg:gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset(config('kafe.logo')) }}" alt="" class="h-16 w-16 rounded-full border-2 border-white/20 object-cover shadow-sm">
                <div>
                    <h3 class="font-display text-2xl font-bold">{{ config('kafe.name') }}</h3>
                    <p class="text-[10px] uppercase tracking-wider text-white/70">Chai · Coffee · Snacks · Meals</p>
                    <p class="mt-1 text-[11px] text-white/70">{{ config('kafe.location') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h4 class="text-sm font-semibold text-white">Quick Links</h4>
            <ul class="mt-4 space-y-2 text-[13px] text-white/80">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ route('home') }}#about" class="hover:text-white">About</a></li>
                <li><a href="{{ route('order.index') }}" class="hover:text-white">Menu</a></li>
                <li><a href="{{ route('home') }}#gallery" class="hover:text-white">Gallery</a></li>
                <li><a href="{{ route('home') }}#contact" class="hover:text-white">Contact</a></li>
            </ul>
        </div>

        <!-- Our Story -->
        <div>
            <h4 class="text-sm font-semibold text-white">Our Story</h4>
            <p class="mt-4 text-[13px] leading-relaxed text-white/80">
                Brewing joy, serving memories — café favourites and celebrations on the Jakhalabandha Highway.
            </p>
            <a href="{{ config('kafe.instagram') }}" target="_blank" rel="noopener" class="mt-4 flex items-center gap-2 text-[13px] font-medium text-white/80 hover:text-white">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                @the_kafe_jakhalabandha
            </a>
        </div>

        <!-- Visit Us -->
        <div>
            <h4 class="text-sm font-semibold text-white">Visit Us</h4>
            <div class="mt-4 flex items-start gap-2 text-[13px] text-white/80">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Jakhalabandha, Assam</span>
            </div>
            <div class="mt-3 flex items-start gap-2 text-[13px] text-white/80">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <span class="block">Open daily</span>
                    <span class="block text-[11px]">Chai & coffee · Snacks · Celebrations</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center justify-between border-t border-white/10 px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-[11px] text-white/60">&copy; {{ date('Y') }} {{ config('kafe.name') }}. All rights reserved.</p>
        <div class="flex items-center gap-3">
            <span class="text-[11px] text-white/60">Follow us</span>
            <a href="{{ config('kafe.instagram') }}" target="_blank" rel="noopener" class="text-white/60 hover:text-white">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
        </div>
    </div>

</footer>
