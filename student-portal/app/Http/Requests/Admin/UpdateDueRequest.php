<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\AdminDueService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in(array_keys(AdminDueService::STATUS_OPTIONS))],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['nullable', 'date'],
            'verification_date' => ['nullable', 'date'],
            'verification_notes' => ['nullable', 'string'],
            'payment_notes' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'string'],
            'network' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
