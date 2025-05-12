<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'delivery_time' => 'required|integer|min:1|max:5',
            'delivery_service' => 'required|integer|min:1|max:5',
            'food_quality' => 'required|integer|min:1|max:5',
            'packing' => 'required|integer|min:1|max:5',
            'overall_experience' => 'required|integer|min:1|max:5',
            'additional_note' => 'nullable|string|max:500',
            'rating_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The specified order does not exist.',
            'delivery_time.required' => 'The delivery time rating is required.',
            'delivery_time.integer' => 'The delivery time rating must be an integer.',
            'delivery_time.min' => 'The delivery time rating must be at least 1.',
            'delivery_time.max' => 'The delivery time rating cannot exceed 5.',
            // Similar messages can be added for other fields if needed
        ];
    }
}