<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LearningModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('dosen') ?? false;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
