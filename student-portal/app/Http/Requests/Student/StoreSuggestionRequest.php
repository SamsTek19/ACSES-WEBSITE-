<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('student') !== null;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:60'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:4096', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }
}
