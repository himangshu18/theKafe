<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ComboController extends Controller
{
    public function index(): View
    {
        return view('admin.combos.index', [
            'combos' => Combo::query()->with('groups.eligibleItems')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.combos.form', [
            'combo' => new Combo,
            'menuItems' => MenuItem::query()->where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function edit(Combo $combo): View
    {
        return view('admin.combos.form', [
            'combo' => $combo->load('groups.eligibleItems'),
            'menuItems' => MenuItem::query()->where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $combo = DB::transaction(function () use ($request, $validated): Combo {
            $combo = Combo::create($this->comboAttributes($validated, Str::slug($validated['name']).'-'.Str::lower(Str::random(6))));
            $this->syncGroups($combo, $validated['groups']);
            $this->storeImage($request, $combo);

            return $combo;
        });

        return redirect()->route('admin.combos.edit', $combo)->with('success', 'Combo saved.');
    }

    public function update(Request $request, Combo $combo): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($request, $combo, $validated): void {
            $combo->update($this->comboAttributes($validated, $combo->slug));
            $combo->groups()->delete();
            $this->syncGroups($combo, $validated['groups']);
            $this->storeImage($request, $combo);
        });

        return back()->with('success', 'Combo updated.');
    }

    public function destroy(Combo $combo): RedirectResponse
    {
        $combo->delete();

        return back()->with('success', 'Combo removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999.99', 'decimal:0,2'],
            'kind' => ['required', 'in:meal,offer'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'groups' => ['required', 'array', 'min:1', 'max:12'],
            'groups.*.name' => ['required', 'string', 'max:100'],
            'groups.*.min_select' => ['required', 'integer', 'min:0', 'max:20'],
            'groups.*.max_select' => ['required', 'integer', 'min:1', 'max:20'],
            'groups.*.included_quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'groups.*.items' => ['required', 'array', 'min:1', 'max:100'],
            'groups.*.items.*.menu_item_id' => ['required', 'integer', 'distinct', 'exists:menu_items,id'],
            'groups.*.items.*.price_adjustment' => ['required', 'numeric', 'min:-999999.99', 'max:99999999.99', 'decimal:0,2'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function comboAttributes(array $validated, string $slug): array
    {
        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'kind' => $validated['kind'],
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $groups
     */
    private function syncGroups(Combo $combo, array $groups): void
    {
        $validMenuItems = MenuItem::query()
            ->whereIn('id', collect($groups)->flatMap(fn ($group) => collect($group['items'])->pluck('menu_item_id'))->all())
            ->where('is_available', true)
            ->pluck('id')
            ->all();

        foreach ($groups as $groupData) {
            if ($groupData['min_select'] > $groupData['max_select'] || count($groupData['items']) < $groupData['min_select'] || count($groupData['items']) < $groupData['max_select']) {
                throw ValidationException::withMessages([
                    'groups' => 'Each combo group must contain enough eligible menu items for its selection limits.',
                ]);
            }

            $eligibleItems = [];
            foreach ($groupData['items'] as $itemData) {
                $itemId = (int) $itemData['menu_item_id'];
                if (! in_array($itemId, $validMenuItems, true)) {
                    throw ValidationException::withMessages([
                        'groups' => 'Combo groups can only include available menu items.',
                    ]);
                }
                $eligibleItems[$itemId] = ['price_adjustment' => $itemData['price_adjustment']];
            }

            $group = $combo->groups()->create([
                'name' => $groupData['name'],
                'min_select' => $groupData['min_select'],
                'max_select' => $groupData['max_select'],
                'included_quantity' => $groupData['included_quantity'],
            ]);
            $group->eligibleItems()->sync($eligibleItems);
        }
    }

    private function storeImage(Request $request, Combo $combo): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        if ($combo->image_path) {
            Storage::disk('public')->delete($combo->image_path);
        }

        $combo->update(['image_path' => $request->file('image')->store('combos', 'public')]);
    }
}
