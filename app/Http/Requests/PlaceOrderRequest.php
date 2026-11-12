<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s().-]{8,20}$/'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'idempotency_key' => ['nullable', 'string', 'size:64'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.menu_item_id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'items.*.menu_variation_id' => ['nullable', 'integer', 'exists:menu_variations,id'],
            'items.*.addon_option_ids' => ['nullable', 'array', 'max:20'],
            'items.*.addon_option_ids.*' => ['integer', 'distinct', 'exists:addon_options,id'],
            'items.*.combo_id' => ['nullable', 'integer', 'exists:combos,id'],
            'items.*.combo_selections' => ['nullable', 'array'],
            'items.*.combo_selections.*' => ['array'],
            'items.*.combo_selections.*.*' => ['integer', 'distinct', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
