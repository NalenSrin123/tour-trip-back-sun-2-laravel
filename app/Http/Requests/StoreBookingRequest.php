<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_schedule_id' => 'required|exists:tour_schedules,id',
            'type'             => 'required|in:Individual,Family,Team',
            'members_count'    => 'nullable|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'participants'     => 'nullable|array',
            'participants.*.name'      => 'required_with:participants|string|max:255',
            'participants.*.sex'       => 'nullable|string|in:male,female,other,Male,Female,Other',
            'participants.*.age_group' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tour_schedule_id.required' => 'The tour schedule field is required.',
            'tour_schedule_id.exists'   => 'The selected tour schedule does not exist.',
            'type.required'             => 'The booking type is required.',
            'type.in'                   => 'The booking type must be Individual, Family, or Team.',
            'members_count.min'         => 'Members count must be at least 1.',
            'participants.*.name.required_with' => 'Participant name is required.',
        ];
    }
}
