<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\TodaySpecial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TodaySpecialController extends Controller
{
    public function index(): View
    {
        return view('admin.specials.index', [
            'specials' => TodaySpecial::query()->with('menuItem')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.specials.form', [
            'special' => new TodaySpecial,
            'menuItems' => MenuItem::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(TodaySpecial $todaySpecial): View
    {
        return view('admin.specials.form', [
            'special' => $todaySpecial,
            'menuItems' => MenuItem::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $special = TodaySpecial::create($validated);

        return redirect()->route('admin.specials.edit', $special)->with('success', 'Today’s special created.');
    }

    public function update(Request $request, TodaySpecial $todaySpecial): RedirectResponse
    {
        $todaySpecial->update($this->validated($request));

        return back()->with('success', 'Today’s special updated.');
    }

    public function destroy(TodaySpecial $todaySpecial): RedirectResponse
    {
        $todaySpecial->delete();

        return back()->with('success', 'Today’s special removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'title' => ['nullable', 'string', 'max:120'],
            'special_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! MenuItem::query()->whereKey($validated['menu_item_id'])->exists()) {
            throw ValidationException::withMessages([
                'menu_item_id' => 'Choose an existing, non-deleted menu item.',
            ]);
        }

        return [
            ...$validated,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'title' => $validated['title'] ?? null,
            'special_price' => $validated['special_price'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ];
    }
}
