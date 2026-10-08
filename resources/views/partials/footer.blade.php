<footer id="contact" class="mt-20 border-t border-kafe-200 bg-kafe-900 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
            <img src="{{ asset(config('kafe.logo')) }}" alt="" class="h-20 w-20 rounded-full object-cover ring-2 ring-kafe-400/50">
            <div>
                <h3 class="font-display text-2xl font-semibold">{{ config('kafe.name') }}</h3>
                <p class="mt-1 text-sm text-kafe-200">{{ config('kafe.tagline') }}</p>
                <p class="mt-1 text-sm text-kafe-300">{{ config('kafe.location') }}</p>
            </div>
        </div>
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-kafe-300">Visit us</h4>
            <p class="mt-3 text-sm text-kafe-100">Brewing joy, serving memories — pure veg bites and celebrations on the Jakhalabandha highway.</p>
            <a href="{{ config('kafe.instagram') }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-white hover:text-kafe-200">
                @the_kafe_jakhalabandha on Instagram
            </a>
        </div>
        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-kafe-300">Quick links</h4>
            <ul class="mt-3 space-y-2 text-sm text-kafe-100">
                <li><a href="{{ route('order.index') }}" class="hover:text-white">Order online</a></li>
                <li><a href="{{ route('booking.create') }}" class="hover:text-white">Book a party</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Owner portal</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-kafe-800 py-6 text-center text-xs text-kafe-400">
        &copy; {{ date('Y') }} {{ config('kafe.name') }}. All rights reserved.
    </div>
</footer>
