<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'party_type' => ['required', Rule::in(array_keys(config('kafe.party_types')))],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required', Rule::in(array_keys(config('kafe.booking_time_slots')))],
            'guest_count' => ['required', 'integer', 'min:1', 'max:200'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
