@extends('layouts.kafe')

@section('title', 'Book a Party — '.config('kafe.name'))

@section('content')
<div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
    <h1 class="section-title">Book a celebration</h1>
    <p class="mt-2 text-kafe-700">Birthdays, anniversaries, or a quiet family dinner — tell us your date and we’ll confirm on WhatsApp.</p>

    @if ($errors->any())
        <div class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('booking.store') }}" class="mt-8 space-y-5 rounded-2xl border border-kafe-200 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-kafe-800">Your name</label>
            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-kafe-800">Phone / WhatsApp</label>
                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-kafe-800">Email (optional)</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-kafe-800">Occasion</label>
            <select name="party_type" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
                @foreach($partyTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('party_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-kafe-800">Date</label>
                <input type="date" name="event_date" value="{{ old('event_date') }}" min="{{ now()->format('Y-m-d') }}" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-kafe-800">Preferred time</label>
                <select name="event_time" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
                    @foreach($timeSlots as $value => $label)
                        <option value="{{ $value }}" @selected(old('event_time') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-kafe-800">Number of guests</label>
            <input type="number" name="guest_count" value="{{ old('guest_count', 10) }}" min="1" max="200" required class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-kafe-800">Special requests</label>
            <textarea name="message" rows="3" class="mt-1 w-full rounded-lg border-kafe-200 focus:border-kafe-500 focus:ring-kafe-500" placeholder="Cake, décor, menu preferences…">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn-primary w-full">Send booking via WhatsApp</button>
    </form>
</div>
@endsection
