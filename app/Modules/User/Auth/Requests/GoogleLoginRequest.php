<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'google_id' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
            'id_token' => ['nullable', 'string'],
        ];
    }
}
