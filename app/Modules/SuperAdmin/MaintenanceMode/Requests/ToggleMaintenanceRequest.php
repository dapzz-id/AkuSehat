<?php

namespace App\Modules\SuperAdmin\MaintenanceMode\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\MaintenanceMode\Enums\MaintenancePlatform;

class ToggleMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'required|in:' . implode(',', MaintenancePlatform::values()),
        ];
    }
}
