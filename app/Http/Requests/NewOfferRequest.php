<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique("offers", "name")->ignore($this->route('offer.id'))
            ],
            'slug'        => ['required', 'string', 'max:190'],
            'amount'      => ['required', 'numeric', 'max:100', new IniAmount()],
            'status'      => ['required', 'numeric', 'max:24'],
            'start_date'  => ['required', 'string', 'max:190'],
            'end_date'    => ['required', 'string', 'max:190'],
            'items'       => ['required', 'array'],
            'items.*'     => ['integer', 'exists:items,id'],
            'image'       => $this->route('offer.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
    

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isNotNull(request('start_date'))) {
                $validator->errors()->add('start_date', 'The start date field is required');
            }

            if (!$this->isNotNull(request('end_date'))) {
                $validator->errors()->add('end_date', 'The end date field is required');
            }

            if ($this->isNotNull(request('start_date')) && strtotime(request('end_date')) < strtotime(request('start_date'))) {
                $validator->errors()->add('end_date', 'To date can\'t be older than Start date.');
            }
            if ($this->isNotNull(request('start_date')) && $this->checkToDate()) {
                $validator->errors()->add('end_date', 'To date can\'t be older than now.');
            }
        });
    }


    private function checkToDate()
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        if (strtotime(request('end_date')) < $today) {
            return true;
        }
    }

    private function isNotNull($value)
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}
