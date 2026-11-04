<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'use_custom_modules' => 'nullable|string',
            'custom_modules' => 'nullable|array',
            'custom_modules.*' => 'string',
            'custom_permissions' => 'nullable|array',
            'custom_permissions.can_create' => 'nullable',
            'custom_permissions.can_read' => 'nullable',
            'custom_permissions.can_update' => 'nullable',
            'custom_permissions.can_delete' => 'nullable',
            'custom_permissions.can_add_user' => 'nullable',
            'custom_permissions.modules' => 'nullable|array',
        ];
    }
}
