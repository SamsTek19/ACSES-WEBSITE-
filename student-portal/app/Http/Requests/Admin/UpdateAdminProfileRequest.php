<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        $adminId = $this->user('admin')?->getKey();

        return [
            'fullname' => ['nullable', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:60', 'unique:users,username,' . $adminId . ',user_id'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $adminId . ',user_id'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
