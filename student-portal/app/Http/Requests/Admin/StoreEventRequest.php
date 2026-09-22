<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'category' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'banner_image' => ['nullable', 'image', 'max:10240'],
            'banner_alt' => ['nullable', 'string', 'max:150'],
            'remove_banner' => ['nullable', 'boolean'],
        ];
    }
}
