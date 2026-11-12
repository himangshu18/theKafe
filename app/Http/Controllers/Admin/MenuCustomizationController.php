<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonGroup;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuCustomizationController extends Controller
{
    public function edit(MenuItem $menuItem): View
    {
        return view('admin.menu.customize', [
            'item' => $menuItem->load([
                'category',
                'images',
                'variations',
                'addonGroups.options',
                'availabilitySchedules',
            ]),
        ]);
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'variations' => ['nullable', 'array', 'max:20'],
            'variations.*.id' => ['nullable', 'integer', 'exists:menu_variations,id'],
            'variations.*.name' => ['required', 'string', 'max:100'],
            'variations.*.price' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'variations.*.is_available' => ['sometimes', 'boolean'],
            'variations.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'addon_groups' => ['nullable', 'array', 'max:12'],
            'addon_groups.*.id' => ['nullable', 'integer', 'exists:addon_groups,id'],
            'addon_groups.*.name' => ['required', 'string', 'max:100'],
            'addon_groups.*.min_select' => ['required', 'integer', 'min:0', 'max:20'],
            'addon_groups.*.max_select' => ['required', 'integer', 'min:1', 'max:20'],
            'addon_groups.*.is_required' => ['sometimes', 'boolean'],
            'addon_groups.*.options' => ['required', 'array', 'min:1', 'max:20'],
            'addon_groups.*.options.*.id' => ['nullable', 'integer', 'exists:addon_options,id'],
            'addon_groups.*.options.*.name' => ['required', 'string', 'max:100'],
            'addon_groups.*.options.*.price_delta' => ['required', 'numeric', 'min:-999999.99', 'max:99999999.99', 'decimal:0,2'],
            'addon_groups.*.options.*.is_available' => ['sometimes', 'boolean'],
            'schedules' => ['nullable', 'array', 'max:21'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'schedules.*.starts_at' => ['required', 'date_format:H:i'],
            'schedules.*.ends_at' => ['required', 'date_format:H:i'],
            'schedules.*.is_available' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer', 'distinct', 'exists:menu_item_images,id'],
        ]);

        $this->validateOwnership($menuItem, $validated);
        $this->validateSchedules($validated['schedules'] ?? []);

        DB::transaction(function () use ($request, $menuItem, $validated): void {
            $variationIds = [];
            foreach ($validated['variations'] ?? [] as $variationData) {
                $variation = isset($variationData['id'])
                    ? $menuItem->variations()->findOrFail($variationData['id'])
                    : $menuItem->variations()->make();
                $variation->fill([
                    'name' => $variationData['name'],
                    'price' => $variationData['price'],
                    'is_available' => (bool) ($variationData['is_available'] ?? false),
                    'sort_order' => $variationData['sort_order'] ?? 0,
                ])->save();
                $variationIds[] = $variation->id;
            }
            $menuItem->variations()->whereNotIn('id', $variationIds ?: [0])->delete();

            $groupIds = [];
            foreach ($validated['addon_groups'] ?? [] as $groupData) {
                if ($groupData['min_select'] > $groupData['max_select']) {
                    throw ValidationException::withMessages([
                        'addon_groups' => 'An add-on group cannot require more selections than its maximum.',
                    ]);
                }

                $group = isset($groupData['id'])
                    ? AddonGroup::query()->findOrFail($groupData['id'])
                    : new AddonGroup;
                $group->fill([
                    'name' => $groupData['name'],
                    'min_select' => $groupData['min_select'],
                    'max_select' => $groupData['max_select'],
                    'is_required' => (bool) ($groupData['is_required'] ?? false),
                    'is_active' => true,
                ])->save();

                $optionIds = [];
                foreach ($groupData['options'] as $optionData) {
                    $option = isset($optionData['id'])
                        ? $group->options()->findOrFail($optionData['id'])
                        : $group->options()->make();
                    $option->fill([
                        'name' => $optionData['name'],
                        'price_delta' => $optionData['price_delta'],
                        'is_available' => (bool) ($optionData['is_available'] ?? false),
                    ])->save();
                    $optionIds[] = $option->id;
                }
                $group->options()->whereNotIn('id', $optionIds ?: [0])->delete();
                $groupIds[] = $group->id;
            }
            $menuItem->addonGroups()->sync($groupIds);

            $menuItem->availabilitySchedules()->delete();
            foreach ($validated['schedules'] ?? [] as $scheduleData) {
                $menuItem->availabilitySchedules()->create([
                    'day_of_week' => $scheduleData['day_of_week'],
                    'starts_at' => $scheduleData['starts_at'].':00',
                    'ends_at' => $scheduleData['ends_at'].':00',
                    'is_available' => (bool) ($scheduleData['is_available'] ?? false),
                ]);
            }

            $removedImages = $menuItem->images()->whereIn('id', $validated['remove_image_ids'] ?? [])->get();
            foreach ($removedImages as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            foreach ($request->file('images', []) as $index => $imageFile) {
                $menuItem->images()->create([
                    'image_path' => $imageFile->store('menu', 'public'),
                    'alt_text' => $menuItem->name,
                    'sort_order' => $menuItem->images()->count() + $index,
                ]);
            }
        });

        return back()->with('success', 'Menu customizations updated.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function validateOwnership(MenuItem $menuItem, array $validated): void
    {
        $variationIds = collect($validated['variations'] ?? [])->pluck('id')->filter()->all();
        $ownedVariationIds = $menuItem->variations()->pluck('id')->all();
        if (array_diff($variationIds, $ownedVariationIds) !== []) {
            throw ValidationException::withMessages([
                'variations' => 'A selected variation does not belong to this menu item.',
            ]);
        }

        $ownedGroupIds = $menuItem->addonGroups()->pluck('addon_groups.id')->all();
        foreach ($validated['addon_groups'] ?? [] as $groupData) {
            if (! empty($groupData['id']) && ! in_array((int) $groupData['id'], $ownedGroupIds, true)) {
                throw ValidationException::withMessages([
                    'addon_groups' => 'A selected add-on group does not belong to this menu item.',
                ]);
            }
            if (empty($groupData['id'])) {
                continue;
            }

            $ownedOptionIds = AddonGroup::query()
                ->findOrFail($groupData['id'])
                ->options()
                ->pluck('id')
                ->all();
            $optionIds = collect($groupData['options'])->pluck('id')->filter()->all();
            if (array_diff($optionIds, $ownedOptionIds) !== []) {
                throw ValidationException::withMessages([
                    'addon_groups' => 'A selected add-on does not belong to its group.',
                ]);
            }
        }

        $imageIds = collect($validated['remove_image_ids'] ?? [])->all();
        $ownedImageIds = $menuItem->images()->pluck('id')->all();
        if (array_diff($imageIds, $ownedImageIds) !== []) {
            throw ValidationException::withMessages([
                'remove_image_ids' => 'A selected image does not belong to this menu item.',
            ]);
        }
    }

    /**
     * @param  list<array{day_of_week: int, starts_at: string, ends_at: string}>  $schedules
     */
    private function validateSchedules(array $schedules): void
    {
        $seen = [];
        foreach ($schedules as $schedule) {
            if ($schedule['ends_at'] <= $schedule['starts_at']) {
                throw ValidationException::withMessages([
                    'schedules' => 'Each availability period must end after it starts.',
                ]);
            }

            $key = $schedule['day_of_week'].'|'.$schedule['starts_at'];
            if (isset($seen[$key])) {
                throw ValidationException::withMessages([
                    'schedules' => 'A weekday and start time can only be scheduled once.',
                ]);
            }
            $seen[$key] = true;
        }
    }
}
