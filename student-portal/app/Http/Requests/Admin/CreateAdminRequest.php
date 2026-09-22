<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'fullname' => ['nullable', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:60', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
