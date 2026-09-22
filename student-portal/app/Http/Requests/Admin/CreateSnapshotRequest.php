<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateSnapshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:system,database'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
