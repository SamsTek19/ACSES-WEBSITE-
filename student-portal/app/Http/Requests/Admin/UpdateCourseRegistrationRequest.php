<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['in_progress', 'submitted', 'approved', 'rejected'])],
            'admin_comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}
