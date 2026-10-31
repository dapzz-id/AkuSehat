<?php

namespace App\Modules\SuperAdmin\ModulePermission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstansiModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active_modules' => 'nullable|array',
            'active_modules.*' => 'nullable|string',
            'modules' => 'nullable|array',
            'modules.*' => 'nullable|string',
            'role_labels' => 'nullable|array',
            'role_labels.*' => 'nullable|string',
            'permissions' => 'nullable|array',
        ];
    }
}
