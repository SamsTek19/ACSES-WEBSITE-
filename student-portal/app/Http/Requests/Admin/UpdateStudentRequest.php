<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        $studentParam = $this->route('student');
        $studentId = $studentParam instanceof User ? $studentParam->getKey() : $studentParam;

        return [
            'username' => ['required', 'string', 'max:60', Rule::unique('users', 'username')->ignore($studentId, 'user_id')],
            'fullname' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($studentId, 'user_id')],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'index_number' => ['nullable', 'string', 'max:60', Rule::unique('users', 'index_number')->ignore($studentId, 'user_id')],
            'class' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'between:1,6'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_seller' => ['nullable', 'boolean'],
        ];
    }
}
