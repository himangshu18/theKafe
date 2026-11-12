@extends('layouts.admin')

@section('heading', 'Customize '.$item->name)

@section('content')
<form method="POST" action="{{ route('admin.menu.customize.update', $item) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <section x-data="{ variations: @js($item->variations->map(fn ($variation) => [
        'id' => $variation->id,
        'name' => $variation->name,
        'price' => (string) $variation->price,
        'is_available' => $variation->is_available,
        'sort_order' => $variation->sort_order,
    ])->values()) }" class="rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div><h2 class="font-semibold">Variations</h2><p class="text-sm text-gray-500">Examples: small, medium, large.</p></div>
            <button type="button" @click="variations.push({id: null, name: '', price: '', is_available: true, sort_order: variations.length})" class="rounded-md border px-3 py-2 text-sm">Add variation</button>
        </div>
        <div class="mt-4 space-y-3">
            <template x-for="(variation, index) in variations" :key="variation.id || `new-${index}`">
                <div class="grid gap-3 rounded-lg border border-gray-100 p-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                    <input type="hidden" :name="`variations[${index}][id]`" :value="variation.id">
                    <input :name="`variations[${index}][name]`" x-model="variation.name" required maxlength="100" placeholder="Name" class="rounded-md border-gray-300 text-sm">
                    <input :name="`variations[${index}][price]`" x-model="variation.price" type="number" min="0" step="0.01" required placeholder="Price" class="rounded-md border-gray-300 text-sm">
                    <input :name="`variations[${index}][sort_order]`" x-model="variation.sort_order" type="hidden">
                    <label class="flex items-center gap-2 text-sm"><input :name="`variations[${index}][is_available]`" x-model="variation.is_available" type="checkbox" value="1" class="rounded"> Available</label>
                    <button type="button" @click="variations.splice(index, 1)" class="text-sm text-red-600">Remove</button>
                </div>
            </template>
            <p x-show="variations.length === 0" class="text-sm text-gray-500">No variations; the base item price is used.</p>
        </div>
    </section>

    <section x-data="{ groups: @js($item->addonGroups->map(fn ($group) => [
        'id' => $group->id,
        'name' => $group->name,
        'min_select' => $group->min_select,
        'max_select' => $group->max_select,
        'is_required' => $group->is_required,
        'options' => $group->options->map(fn ($option) => [
            'id' => $option->id,
            'name' => $option->name,
            'price_delta' => (string) $option->price_delta,
            'is_available' => $option->is_available,
        ])->values(),
    ])->values()) }" class="rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div><h2 class="font-semibold">Add-ons and customization groups</h2><p class="text-sm text-gray-500">Configure choices and selection limits for this item.</p></div>
            <button type="button" @click="groups.push({id: null, name: '', min_select: 0, max_select: 1, is_required: false, options: [{id: null, name: '', price_delta: '0.00', is_available: true}]})" class="rounded-md border px-3 py-2 text-sm">Add group</button>
        </div>
        <div class="mt-4 space-y-4">
            <template x-for="(group, groupIndex) in groups" :key="group.id || `new-${groupIndex}`">
                <fieldset class="rounded-lg border border-gray-200 p-4">
                    <input type="hidden" :name="`addon_groups[${groupIndex}][id]`" :value="group.id">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <input :name="`addon_groups[${groupIndex}][name]`" x-model="group.name" required maxlength="100" placeholder="Group name" class="rounded-md border-gray-300 text-sm">
                        <label class="text-xs text-gray-600">Minimum<select :name="`addon_groups[${groupIndex}][min_select]`" x-model.number="group.min_select" class="mt-1 w-full rounded-md border-gray-300 text-sm"><template x-for="n in 20"><option :value="n - 1" x-text="n - 1"></option></template></select></label>
                        <label class="text-xs text-gray-600">Maximum<select :name="`addon_groups[${groupIndex}][max_select]`" x-model.number="group.max_select" class="mt-1 w-full rounded-md border-gray-300 text-sm"><template x-for="n in 20"><option :value="n" x-text="n"></option></template></select></label>
                        <label class="flex items-center gap-2 text-sm"><input :name="`addon_groups[${groupIndex}][is_required]`" x-model="group.is_required" type="checkbox" value="1" class="rounded"> Required</label>
                    </div>
                    <div class="mt-4 space-y-2">
                        <template x-for="(option, optionIndex) in group.options" :key="option.id || `new-${optionIndex}`">
                            <div class="grid gap-2 sm:grid-cols-[1fr_1fr_auto_auto]">
                                <input type="hidden" :name="`addon_groups[${groupIndex}][options][${optionIndex}][id]`" :value="option.id">
                                <input :name="`addon_groups[${groupIndex}][options][${optionIndex}][name]`" x-model="option.name" required maxlength="100" placeholder="Option name" class="rounded-md border-gray-300 text-sm">
                                <input :name="`addon_groups[${groupIndex}][options][${optionIndex}][price_delta]`" x-model="option.price_delta" type="number" step="0.01" required placeholder="Price change" class="rounded-md border-gray-300 text-sm">
                                <label class="flex items-center gap-2 text-xs"><input :name="`addon_groups[${groupIndex}][options][${optionIndex}][is_available]`" x-model="option.is_available" type="checkbox" value="1" class="rounded"> Available</label>
                                <button type="button" @click="group.options.splice(optionIndex, 1)" class="text-xs text-red-600">Remove</button>
                            </div>
                        </template>
                        <button type="button" @click="group.options.push({id: null, name: '', price_delta: '0.00', is_available: true})" class="text-xs font-medium text-kafe-800">+ Add option</button>
                    </div>
                    <button type="button" @click="groups.splice(groupIndex, 1)" class="mt-3 text-xs text-red-600">Remove group</button>
                </fieldset>
            </template>
            <p x-show="groups.length === 0" class="text-sm text-gray-500">No add-on groups configured.</p>
        </div>
    </section>

    <section x-data="{ schedules: @js($item->availabilitySchedules->map(fn ($schedule) => [
        'day_of_week' => $schedule->day_of_week,
        'starts_at' => substr($schedule->starts_at, 0, 5),
        'ends_at' => substr($schedule->ends_at, 0, 5),
        'is_available' => $schedule->is_available,
    ])->values()) }" class="rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div><h2 class="font-semibold">Availability schedule</h2><p class="text-sm text-gray-500">No schedule means this item can be ordered any time.</p></div>
            <button type="button" @click="schedules.push({day_of_week: 0, starts_at: '09:00', ends_at: '17:00', is_available: true})" class="rounded-md border px-3 py-2 text-sm">Add time</button>
        </div>
        <div class="mt-4 space-y-3">
            <template x-for="(schedule, index) in schedules" :key="index">
                <div class="grid gap-3 sm:grid-cols-[1fr_1fr_1fr_auto_auto]">
                    <select :name="`schedules[${index}][day_of_week]`" x-model.number="schedule.day_of_week" class="rounded-md border-gray-300 text-sm">
                        <template x-for="(day, dayIndex) in ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']" :key="dayIndex"><option :value="dayIndex" x-text="day"></option></template>
                    </select>
                    <input :name="`schedules[${index}][starts_at]`" x-model="schedule.starts_at" type="time" required class="rounded-md border-gray-300 text-sm">
                    <input :name="`schedules[${index}][ends_at]`" x-model="schedule.ends_at" type="time" required class="rounded-md border-gray-300 text-sm">
                    <label class="flex items-center gap-2 text-sm"><input :name="`schedules[${index}][is_available]`" x-model="schedule.is_available" type="checkbox" value="1" class="rounded"> Available</label>
                    <button type="button" @click="schedules.splice(index, 1)" class="text-sm text-red-600">Remove</button>
                </div>
            </template>
        </div>
    </section>

    <section class="rounded-xl bg-white p-5 shadow-sm">
        <h2 class="font-semibold">Menu images</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_2fr]">
            <label class="text-sm">Add images<input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full text-sm"></label>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach($item->images as $image)
                    <label class="relative rounded-lg border p-2 text-xs">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->image_path) }}" alt="{{ $image->alt_text ?? $item->name }}" class="h-20 w-full rounded object-cover">
                        <span class="mt-1 block truncate">{{ $image->alt_text }}</span>
                        <span class="mt-1 flex items-center gap-1"><input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}"> Remove</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.menu.index') }}" class="text-sm font-medium text-kafe-800 underline">Back to menu</a>
        <button class="rounded-lg bg-kafe-800 px-5 py-3 text-sm font-semibold text-white">Save customizations</button>
    </div>
</form>
@endsection
