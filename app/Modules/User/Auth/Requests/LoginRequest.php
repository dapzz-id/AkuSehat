<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
            'kebijakan_privasi' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'kebijakan_privasi.required' => 'Anda wajib membaca dan menyetujui Kebijakan Privasi terlebih dahulu.',
            'kebijakan_privasi.accepted' => 'Anda wajib menyetujui Kebijakan Privasi untuk melanjutkan.',
        ];
    }
}
