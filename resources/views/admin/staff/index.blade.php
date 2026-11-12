@extends('layouts.admin')

@section('heading', 'Staff access')

@section('content')
<section class="rounded-xl bg-white p-5 shadow-sm">
    <h2 class="font-semibold">Create staff account</h2>
    <p class="mt-1 text-sm text-gray-500">Share the initial password securely with the staff member. Public registration remains disabled.</p>
    <form method="POST" action="{{ route('admin.staff.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
        @csrf
        <input name="name" required maxlength="120" value="{{ old('name') }}" placeholder="Full name" class="rounded-md border-gray-300 text-sm">
        <input name="email" type="email" required maxlength="255" value="{{ old('email') }}" placeholder="Email" class="rounded-md border-gray-300 text-sm">
        <select name="admin_role" required class="rounded-md border-gray-300 text-sm">
            @foreach(['manager', 'cashier', 'kitchen', 'accountant'] as $role)<option value="{{ $role }}">{{ ucfirst($role) }}</option>@endforeach
        </select>
        <input name="password" type="password" required minlength="12" placeholder="Initial password (12+ characters)" class="rounded-md border-gray-300 text-sm">
        <input name="password_confirmation" type="password" required minlength="12" placeholder="Confirm password" class="rounded-md border-gray-300 text-sm sm:col-span-2">
        <button class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Create staff account</button>
    </form>
</section>

<section class="mt-6 space-y-3">
    @foreach($staff as $user)
        <article class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-white p-4 shadow-sm">
            <div>
                <p class="font-medium">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }} · {{ ucfirst($user->admin_role) }}</p>
            </div>
            @if($user->admin_role !== 'owner' && $user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.staff.update', $user) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="admin_role" class="rounded-md border-gray-300 text-sm">
                        @foreach(['manager', 'cashier', 'kitchen', 'accountant', 'customer'] as $role)
                            <option value="{{ $role }}" @selected($role === $user->admin_role)>{{ ucfirst($role === 'customer' ? 'Revoke admin' : $role) }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-md border border-kafe-200 px-3 py-2 text-xs font-medium text-kafe-800">Save access</button>
                </form>
            @endif
        </article>
    @endforeach
</section>
@endsection
