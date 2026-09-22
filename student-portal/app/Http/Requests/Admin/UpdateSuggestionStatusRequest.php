<?php

namespace App\Http\Requests\Admin;

use App\Enums\SuggestionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSuggestionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(SuggestionStatus::values())],
        ];
    }
}
