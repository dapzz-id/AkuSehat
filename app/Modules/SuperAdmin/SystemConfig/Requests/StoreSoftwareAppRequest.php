<?php

namespace App\Modules\SuperAdmin\SystemConfig\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwareStatus;

class StoreSoftwareAppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('link')) {
            $link = trim($this->link);
            if (!preg_match('~^(?:f|ht)tps?://~i', $link)) {
                $this->merge(['link' => 'https://' . $link]);
            }
        }
    }

    public function rules(): array
    {
        $isWeb = $this->input('platform') === SoftwarePlatform::WEB->value;

        return [
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'min_version' => 'nullable|string|max:50',
            'platform' => 'required|in:' . implode(',', SoftwarePlatform::values()),
            'link' => $isWeb ? 'nullable|url|max:1000' : 'required|url|max:1000',
            'developer' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'changelog' => 'nullable|string|max:5000',
            'release_date' => 'required|date',
            'status' => 'required|in:' . implode(',', SoftwareStatus::values()),
            'is_critical' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:apk,aab,zip,rar,pdf|max:102400',
        ];
    }
}
