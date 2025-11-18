<?php

namespace App\Http\Requests;

use App\Enums\Ask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;



class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
	protected function failedValidation(Validator $validator)
{
    $allMessages = collect($validator->errors()->all())->implode(' ');

    throw new HttpResponseException(response()->json([
        'success' => false,
        'message' => $allMessages, // All errors as a single string
        'errors' => $validator->errors() // Detailed error messages
    ], 422));
}

    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['nullable', 'string', 'max:255'],
            'email'        => [
                'nullable',
                'string',
                'max:255',
                Rule::unique("users", "email")
                    ->whereNull('deleted_at')
                    ->where('is_guest', Ask::NO)
            ],
            'phone'        => [
                'required',
                'numeric',
                Rule::unique("users", "phone")
                    ->whereNull('deleted_at')
                    ->where('is_guest', Ask::NO)
            ],
            'country_code' => ['required', 'numeric'],
            'password'     => ['required', 'string', 'min:6'],
            'whatsapp_phone_number' => ['nullable', 'numeric'],
            'referral_code' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('users', 'my_referral_code')
                    ->whereNull('deleted_at')
            ],
            'birthday' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === '0000-01-01') {
                        return;
                    }
                    // You can add additional logic if needed
                },
            ],
            'whatsapp_country_code' => ['nullable', 'numeric'],
            'discount_id_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
        ];
    }
}
