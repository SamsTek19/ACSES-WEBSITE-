<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:60', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'index_number' => ['nullable', 'string', 'max:60', 'unique:users,index_number'],
            'class' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'between:1,6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_seller' => ['nullable', 'boolean'],
        ];
    }
}
