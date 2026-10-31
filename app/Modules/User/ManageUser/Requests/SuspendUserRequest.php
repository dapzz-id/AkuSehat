<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuspendUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration' => 'required|string',
            'custom_date' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
        ];
    }
}
