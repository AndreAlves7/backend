<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
                'vcard' => 'required|numeric|digits:9|starts_with:9',
                'name' => 'required|string',
                'type' => 'required|in:C,D'
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
            'vcard.required' => 'A vcard number is required',
            'vcard.numeric' => 'The vcard number must be numeric',
            'vcard.digits' => 'The vcard number must have 9 digits',
            'vcard.starts_with' => 'The vcard number must start with 9',
            'name.required' => 'A name is required',
            'type.required' => 'A type is required',
            'type.in' => 'A type must be C or D',
        ];
    }
}
