<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkInstansiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invite_code' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'invite_code.required' => 'Kode undangan instansi wajib diisi.',
        ];
    }
}
