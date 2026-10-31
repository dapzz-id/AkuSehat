<?php

namespace App\Modules\SuperAdmin\SystemConfig\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwareStatus;

class FilterSoftwareAppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'nullable|in:' . implode(',', SoftwarePlatform::values()),
            'status' => 'nullable|in:' . implode(',', SoftwareStatus::values()),
            'search' => 'nullable|string|max:100',
        ];
    }
}
