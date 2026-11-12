<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        return view('admin.staff.index', [
            'staff' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_role' => ['required', Rule::in(['manager', 'cashier', 'kitchen', 'accountant'])],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => true,
            'admin_role' => $validated['admin_role'],
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'Staff account created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->is_admin && $user->admin_role !== 'owner' && $user->id !== $request->user()->id, 404);
        $validated = $request->validate([
            'admin_role' => ['required', Rule::in(['manager', 'cashier', 'kitchen', 'accountant', 'customer'])],
        ]);

        $user->update([
            'admin_role' => $validated['admin_role'],
            'is_admin' => $validated['admin_role'] !== 'customer',
        ]);

        return back()->with('success', 'Staff access updated.');
    }
}
