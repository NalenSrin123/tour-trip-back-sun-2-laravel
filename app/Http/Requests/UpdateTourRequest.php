<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourRequest extends FormRequest
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
        // Safely extract the ID whether Laravel returns the Model object or a raw string
        $tour = $this->route('id');
        $tourId = $tour instanceof \App\Models\Tour ? $tour->id : $tour;

        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:tours,slug,' . $tourId,
            'base_price' => 'sometimes|required|numeric|min:0',
            'price_override' => 'sometimes|nullable|numeric|min:0',
            'duration_days' => 'sometimes|required|integer|min:1',
            'duration_nights' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:draft,published,archived',
            'category_id' => 'sometimes|required|exists:categories,id',
            'destination_id' => 'sometimes|required|exists:destinations,id',
        ];
    }
}
