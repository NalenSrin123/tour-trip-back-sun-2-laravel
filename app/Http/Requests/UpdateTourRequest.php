<?php

namespace App\Http\Requests;

use App\Models\Tour;
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
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('tours', 'slug')->ignore($this->route('tour')),
            ],
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
