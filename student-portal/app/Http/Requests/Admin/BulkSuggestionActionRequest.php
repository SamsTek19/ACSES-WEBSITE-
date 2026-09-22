<?php

namespace App\Http\Requests\Admin;

use App\Enums\SuggestionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkSuggestionActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['update_status'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:suggestions,id'],
            'status' => [Rule::requiredIf(fn () => $this->input('action') === 'update_status'), 'nullable', Rule::in(SuggestionStatus::values())],
            'return_url' => ['nullable', 'url'],
        ];
    }
}
