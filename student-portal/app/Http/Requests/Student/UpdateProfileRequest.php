<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('student') !== null;
    }

    public function rules(): array
    {
        return [
            'phone_number' => ['nullable', 'string', 'max:25'],
            'pending_email' => ['nullable', 'email', 'max:150'],
            'profile_picture' => ['nullable', 'image', 'max:4096', 'mimes:jpg,jpeg,png,webp'],
            'profile_picture_cropped' => ['nullable', 'string'],
            'current_password' => ['required_with:password', 'nullable', 'current_password:student'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'remove_profile_picture' => ['nullable', 'boolean'],
        ];
    }
}
