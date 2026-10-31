<?php

namespace App\Modules\SuperAdmin\ModulePermission\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\ModulePermission\Enums\ModuleAccessStatus;

class UpdateGlobalModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modules' => 'required|array',
            'modules.*.status' => 'required|in:' . implode(',', ModuleAccessStatus::values()),
            'modules.*.maintenance_message' => 'nullable|string|max:1000',
            'modules.*.tester_user_ids' => 'nullable|array',
        ];
    }
}
