<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemRealtimeSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'nullable|string|max:30',
            'current_version' => 'nullable|string|max:30',
        ];
    }
}

