<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Base64Services;

class PatchViewAuthUserRequest extends FormRequest
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
        return [
            'name' => 'sometimes|string|max:255|nullable',
            'email' => 'sometimes|email|max:255|nullable',
            'confirmation_code' => 'sometimes|string|min:3|nullable',
            'password' => 'sometimes|string|min:3|nullable',
            'profilePhoto' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048|nullable',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $photo_url = $this->photo_url ?? null;
            if ($photo_url) {
                $base64Service = new Base64Services();
                $mimeType = $base64Service->mimeType($photo_url);
                if (!in_array($mimeType, ['image/png', 'image/jpg', 'image/jpeg'])) {
                    $validator->errors()->add('photo_url', 'File type not supported (only supports "png" and "jpeg" images).');
                }
            }
        });
    }
}
