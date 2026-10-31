<?php

namespace App\Modules\SuperAdmin\ModulePermission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterTesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tester_search' => 'nullable|string|max:100',
        ];
    }
}
