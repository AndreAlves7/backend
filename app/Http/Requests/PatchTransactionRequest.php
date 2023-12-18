<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatchTransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //description and category_id are optional but category_id must be valid
        return [
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
