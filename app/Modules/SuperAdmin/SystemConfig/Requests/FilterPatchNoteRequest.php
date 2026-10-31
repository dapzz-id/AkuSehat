<?php

namespace App\Modules\SuperAdmin\SystemConfig\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;

class FilterPatchNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'nullable|in:' . implode(',', SoftwarePlatform::values()),
        ];
    }
}
