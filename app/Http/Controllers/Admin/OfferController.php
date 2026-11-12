<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        return view('admin.offers.index', [
            'offers' => Offer::query()->with('menuItems')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.offers.form', [
            'offer' => new Offer,
            'menuItems' => MenuItem::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(Offer $offer): View
    {
        return view('admin.offers.form', [
            'offer' => $offer->load('menuItems'),
            'menuItems' => MenuItem::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $offer = Offer::create($this->attributes($validated));
        $offer->menuItems()->sync($validated['menu_item_ids']);

        return redirect()->route('admin.offers.edit', $offer)->with('success', 'Offer created.');
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $validated = $this->validated($request);
        $offer->update($this->attributes($validated));
        $offer->menuItems()->sync($validated['menu_item_ids']);

        return back()->with('success', 'Offer updated.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return back()->with('success', 'Offer removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'menu_item_ids' => ['required', 'array', 'min:1', 'max:500'],
            'menu_item_ids.*' => ['required', 'integer', 'distinct', 'exists:menu_items,id'],
        ]);

        if ($validated['discount_type'] === 'percentage' && (float) $validated['discount_value'] > 100) {
            throw ValidationException::withMessages([
                'discount_value' => 'A percentage discount cannot exceed 100%.',
            ]);
        }

        if (MenuItem::query()->whereIn('id', $validated['menu_item_ids'])->count() !== count($validated['menu_item_ids'])) {
            throw ValidationException::withMessages([
                'menu_item_ids' => 'Offers can only target existing menu items.',
            ]);
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function attributes(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ];
    }
}
