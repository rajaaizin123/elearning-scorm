<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSCORMPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('dosen', 'admin') ?? false;
    }

    public function rules(): array
    {
        $maxKb = (int) config('scorm.upload_max_kb', 512000);

        return [
            'learning_module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'package' => ['required', 'file', 'mimes:zip', "max:{$maxKb}"],
        ];
    }
}
