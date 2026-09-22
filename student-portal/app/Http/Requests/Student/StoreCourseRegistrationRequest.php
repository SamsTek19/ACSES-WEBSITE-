<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('student') !== null;
    }

    public function rules(): array
    {
        return [
            'registration_pdf' => ['required', 'file', 'mimes:pdf', 'max:8192'],
        ];
    }
}
