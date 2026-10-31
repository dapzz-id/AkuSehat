<?php

namespace App\Modules\SuperAdmin\Terminal\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteCommandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'command' => ['required', 'string', 'max:1000'],
            'confirmed' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'command.required' => 'Perintah terminal wajib diisi.',
            'command.max' => 'Perintah terminal tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
