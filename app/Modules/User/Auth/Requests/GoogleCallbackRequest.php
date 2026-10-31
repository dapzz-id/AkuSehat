<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string',
            'simulated' => 'nullable|string',
            'email' => 'nullable|string',
            'name' => 'nullable|string',
        ];
    }
}
