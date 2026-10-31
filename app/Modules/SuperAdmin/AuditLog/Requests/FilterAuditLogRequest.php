<?php

namespace App\Modules\SuperAdmin\AuditLog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterAuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'nullable|string|max:50',
            'range' => 'nullable|string|in:today,7,14,30,all',
            'search' => 'nullable|string|max:100',
        ];
    }
}
