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
            'active_modules.*' => 'string',
            'permissions' => 'nullable|array',
            'permissions.can_create' => 'nullable',
            'permissions.can_read' => 'nullable',
            'permissions.can_update' => 'nullable',
            'permissions.can_delete' => 'nullable',
            'permissions.can_add_user' => 'nullable',
            'permissions.modules' => 'nullable|array',
            'description' => 'nullable|string|max:255',
        ];
    }
}
