<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\AdminAnnouncementService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['required', Rule::in(array_keys(AdminAnnouncementService::TYPES))],
            'priority' => ['required', Rule::in(array_keys(AdminAnnouncementService::PRIORITIES))],
            'target_type' => ['required', Rule::in(array_keys(AdminAnnouncementService::TARGET_TYPES))],
            'classes' => ['array', Rule::requiredIf(fn () => in_array($this->input('target_type'), ['class', 'class_year'], true))],
            'classes.*' => ['string'],
            'years' => ['array', Rule::requiredIf(fn () => in_array($this->input('target_type'), ['year', 'class_year'], true))],
            'years.*' => ['integer'],
            'student_ids' => ['array', Rule::requiredIf(fn () => $this->input('target_type') === 'student')],
            'student_ids.*' => ['integer', 'exists:users,user_id'],
        ];
    }
}
