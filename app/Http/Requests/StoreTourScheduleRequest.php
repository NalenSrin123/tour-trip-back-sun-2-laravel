<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],

            // 1. Tour departure must be in the future
            'start_datetime' => [
                'required',
                'date',
                'after:now',
            ],

            // 2. Tour end must be after departure
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime',
            ],

            // 3. Cutoff must be in the future AND before departure
            'booking_cutoff_datetime' => [
                'required',
                'date',
                'after:now',
                'before:start_datetime',
            ],

            // 4. Capacities
            'min_capacity' => ['required', 'integer', 'min:1'],
            'max_capacity' => ['required', 'integer', 'gte:min_capacity'],

            // 5. Pricing & Details
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:published,confirmed,canceled,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
