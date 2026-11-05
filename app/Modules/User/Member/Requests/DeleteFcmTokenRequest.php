<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fcm_token' => 'nullable|string',
        ];
    }
}
