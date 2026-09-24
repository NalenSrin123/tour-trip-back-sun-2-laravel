<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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

    protected function prepareForValidation(): void
    {
        // If the request has a title, generate a slug from it and merge it into the request
        if ($this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }
    }
    public function rules(): array
    {
        $tourId = $this->route('tour') ? $this->route('tour')->id : null;
        
        return [
            'category_id' => 'required|exists:categories,id',
            'destination_id' => 'required|exists:destinations,id',
            'title' => 'required|string|max:255',

            'slug' => 'nullable|string|max:255|unique:tours,slug'. $tourId,
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
