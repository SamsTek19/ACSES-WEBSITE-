<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkCourseRegistrationActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['update_status', 'download_documents'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:course_registrations,id'],
            'status' => [Rule::requiredIf(fn () => $this->input('action') === 'update_status'), 'nullable', Rule::in(['in_progress', 'submitted', 'approved', 'rejected'])],
            'admin_comment' => ['nullable', 'string', 'max:500'],
            'return_url' => ['nullable', 'url'],
        ];
    }
}
