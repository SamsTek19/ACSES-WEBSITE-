<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'resource_type' => ['required', Rule::in(['link', 'file', 'external'])],
            'content_type' => ['required', Rule::in($this->contentTypes())],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,mp4,mov,avi', 'max:51200'],
            'icon' => ['nullable', 'string', 'max:60'],
            'target_classes' => ['nullable', 'array'],
            'target_classes.*' => [Rule::in($this->classes())],
            'target_years' => ['nullable', 'array'],
            'target_years.*' => [Rule::in($this->years())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('resource_type');

            if (in_array($type, ['link', 'external'], true) && ! $this->filled('cta_url') && ! $this->hasFile('file')) {
                $validator->errors()->add('cta_url', __('Provide a URL or upload a file for this resource.'));
            }
        });
    }

    private function classes(): array
    {
        return ['Cyber Security', 'Computer Science', 'Information System'];
    }

    private function years(): array
    {
        return ['1', '2', '3', '4'];
    }

    private function contentTypes(): array
    {
        return [
            'handout',
            'past_question',
            'lecture_slide',
            'video',
            'link',
            'guide',
            'policy',
            'other',
        ];
    }
}
