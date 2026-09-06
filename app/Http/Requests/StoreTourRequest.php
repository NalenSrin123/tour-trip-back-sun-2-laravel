<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'destination_id' => 'required|exists:destinations,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tours,slug',
            'duration_days' => 'nullable|integer|min:1',
            'duration_nights' => 'nullable|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'price_override' => 'nullable|numeric|min:0',
            'status' => 'required|in:published,draft,archived',
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price field is required.',
            'base_price.numeric' => 'The base price must be a number.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either published, draft, or archived.',
        ];
    }
}
