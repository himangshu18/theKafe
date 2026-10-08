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
                <a href="{{ route('admin.menu.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.menu.*') ? 'bg-kafe-800' : '' }}">Menu items</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.categories.*') ? 'bg-kafe-800' : '' }}">Categories</a>
                <a href="{{ route('admin.bookings.index') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800 {{ request()->routeIs('admin.bookings.*') ? 'bg-kafe-800' : '' }}">Bookings</a>
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-kafe-800">View website</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left hover:bg-kafe-800">Log out</button>
                </form>
            </nav>
        </aside>

        <div class="flex-1">
            <header class="border-b border-kafe-100 bg-white px-4 py-4 shadow-sm md:px-8">
                <h1 class="text-lg font-semibold text-kafe-900">@yield('heading', 'Dashboard')</h1>
            </header>
            <main class="p-4 md:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-kafe-100 px-4 py-3 text-sm text-kafe-900">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
