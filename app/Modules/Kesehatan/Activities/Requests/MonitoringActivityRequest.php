<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'user_id' => 'nullable|string',
            'activity_type' => 'nullable|string|max:50',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
