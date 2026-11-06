<?php

namespace App\Modules\SuperAdmin\AuditLog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCrashReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'error_name' => 'nullable|string|max:120',
            'error_message' => 'required|string|max:2000',
            'stack_trace' => 'nullable|string',
            'component_stack' => 'nullable|string',
            'app_version' => 'nullable|string|max:30',
            'build_number' => 'nullable',
            'platform' => 'nullable|string|max:30',
            'platform_version' => 'nullable|string|max:50',
            'device_model' => 'nullable|string|max:100',
            'device_brand' => 'nullable|string|max:100',
            'user_id' => 'nullable|string|max:50',
            'user_name' => 'nullable|string|max:100',
            'user_role' => 'nullable|string|max:50',
            'instansi_name' => 'nullable|string|max:100',
            'screen_name' => 'nullable|string|max:100',
        ];
    }
}

