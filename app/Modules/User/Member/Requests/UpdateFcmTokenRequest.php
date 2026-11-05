<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fcm_token' => 'required|string',
            'platform' => 'nullable|string|max:20',
            'device_model' => 'nullable|string|max:100',
        ];
    }
}
