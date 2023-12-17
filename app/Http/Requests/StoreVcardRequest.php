<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVcardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // "phone_number" – primary key – the phone number identifies the vcard. All phone numbers
        // have exactly 9 digits and start with the digit 9 (format for Portuguese phone numbers).
        // • "name" – name of the vCard owner.
        // • "email" – email of the vCard owner.
        // • "photo_url" – (optional) photo of the vCard owner - relative url for the image of the photo.
        // EI – DAD 2023/24 - Project 13
        // • "password" – hash for the vCard password. The password is used as a part of the credential
        // (phone number + password) for the application authentication (access to a specific vCard).
        // • "confirmation_code" – hash for the vCard confirmation code. The confirmation code has 3
        // digits and is used to confirm all transactions executed from the vCard.
        // • "blocked" – Indicates whether the vCard is blocked or not. Default value (when the vCard is
        // created) is false, but the administrators can block (blocked = true) any vCard. When the vCard
        // is blocked, the vCard cannot be associated to any transaction (credit or debit) and the owner
        // cannot access any application associated to that vCard.
        // • "balance" – current balance (amount of money) of the card. Value cannot be negative (value
        // is always >= 0). Value of the balance is always zero when the vCard is created.
        // • "max_debit" – the maximum value for any debit transaction of the vCard. The default (when
        // the vCard is created) is 5000€. Only the administrators can change this value.
        // • "custom_options" – json data – this column can be used to add any custom option
        // (configuration or other types of options) to the vCard. Students are free to ignore or add any
        // type of data to this column (json data is not restricted to any structure).
        // • "custom_data" – json data – this column can be used to add any custom data to the vCard.
        // Students are free to ignore or add any type of data to this column (json data is not restricted to
        // any structure).
        return
            [
                //unique phone_number
                'phone_number' => 'required|numeric|digits:9|starts_with:9|unique:vcards,phone_number',
                'name' => 'required|string',
                'email' => 'required|email',
                'photo_url' => 'string',
                'password' => 'required|string',
                'confirmation_code' => 'required|numeric|digits:3',
                // 'blocked' => 'required|boolean',
                // 'balance' => 'required|numeric|min:0',
                // 'max_debit' => 'required|numeric|min:0',
                // 'custom_options' => 'json',
                // 'custom_data' => 'json',
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
            'phone_number.unique' => 'This phone number is already in use.',
            'phone_number.numeric' => 'A phone number must be numeric',
            'phone_number.digits' => 'A phone number must have 9 digits',
            'phone_number.starts_with' => 'A phone number must start with 9',
            'name.required' => 'A name is required',
            'email.required' => 'An email is required',
            'email.email' => 'An email must be valid',
            'photo_url.string' => 'A photo url must be a string',
            'password.required' => 'A password is required',
            'confirmation_code.required' => 'A confirmation code is required',
            'confirmation_code.numeric' => 'A confirmation code must be numeric',
            'confirmation_code.digits' => 'A confirmation code must have 3 digits',
            // 'blocked.required' => 'A blocked is required',
            // 'blocked.boolean' => 'A blocked must be boolean',
            // 'balance.required' => 'A balance is required',
            // 'balance.numeric' => 'A balance must be numeric',
            // 'balance.min' => 'A balance must be greater than or equal to 0',
            // 'max_debit.required' => 'A max debit is required',
            // 'max_debit.numeric' => 'A max debit must be numeric',
            // 'max_debit.min' => 'A max debit must be greater than or equal to 0',
            // 'custom_options.json' => 'A custom options must be json',
            // 'custom_data.json' => 'A custom data must be json',
        ];
    }
}
