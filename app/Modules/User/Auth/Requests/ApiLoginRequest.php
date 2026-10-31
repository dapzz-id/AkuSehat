<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $loginValue = $this->input('login') ?? $this->input('username') ?? $this->input('email');
        if ($loginValue) {
            $this->merge(['login' => $loginValue]);
        }
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ];
    }
}
