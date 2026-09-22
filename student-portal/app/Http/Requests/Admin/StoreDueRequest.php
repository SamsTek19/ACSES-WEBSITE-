<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\StudentAccountService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        $filters = app(StudentAccountService::class)->filterOptions();

        $classes = $filters['classes'] instanceof \Illuminate\Support\Collection
            ? $filters['classes']->values()->all()
            : (array) $filters['classes'];

        $years = $filters['years'] instanceof \Illuminate\Support\Collection
            ? $filters['years']->values()->all()
            : (array) $filters['years'];

        $rules = [
            'description' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'due_date' => ['required', 'date'],
            'base_amount' => ['required', 'numeric', 'min:0'],
            'amounts' => ['nullable', 'array'],
        ];

        foreach ($classes as $class) {
            $rules["amounts.$class"] = ['nullable', 'array'];
            foreach ($years as $year) {
                $rules["amounts.$class.$year"] = ['nullable', 'numeric', 'min:0'];
            }
        }

        return $rules;
    }
}
