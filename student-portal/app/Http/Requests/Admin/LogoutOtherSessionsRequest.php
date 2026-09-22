<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LogoutOtherSessionsRequest extends FormRequest
{
    protected $errorBag = 'logout';

    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:admin'],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => __('current password'),
        ];
    }
}
