<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return
            [
                'phone_number' => 'required|numeric|digits:9|starts_with:9',
                'name' => 'required|string',
                'email' => 'required|email',
                'photo_url' => 'string|nullable',
                'blocked' => 'required|boolean',
                'max_debit' => 'required|numeric|min:0',
            ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone_number.required' => 'A phone number is required',
            'phone_number.numeric' => 'A phone number must be numeric',
            'phone_number.digits' => 'A phone number must have 9 digits',
            'phone_number.starts_with' => 'A phone number must start with 9',
            'name.required' => 'A name is required',
            'email.required' => 'An email is required',
            'email.email' => 'An email must be valid',
            'photo_url.string' => 'A photo url must be a string',
            'blocked.required' => 'A blocked is required',
            'blocked.boolean' => 'A blocked must be boolean',
            'max_debit.required' => 'A max debit is required',
            'max_debit.numeric' => 'A max debit must be numeric',
            'max_debit.min' => 'A max debit must be greater than or equal to 0',
        ];
    }
}
