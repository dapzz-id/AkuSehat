<?php

namespace App\Modules\Kesehatan\VitalLogs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterVitalLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'date' => 'nullable|date',
            'search' => 'nullable|string|max:100',
        ];
    }
}
