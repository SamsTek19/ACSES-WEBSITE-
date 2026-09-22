<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicTimelineEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'starts_at' => ['required', 'date'],
            'academic_year' => ['nullable', 'string', 'max:15'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
