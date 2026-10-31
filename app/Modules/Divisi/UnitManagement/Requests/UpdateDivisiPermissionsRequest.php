<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisiPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'use_custom_modules' => 'nullable|string',
            'active_modules' => 'nullable|array',
            'permissions' => 'nullable|array',
            'description' => 'nullable|string|max:255',
        ];
    }
}
