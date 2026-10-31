<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'tab' => 'nullable|string|max:50',
            'divisi' => 'nullable|string',
            'jk' => 'nullable|string|in:L,P,all',
            'status' => 'nullable|string|max:20',
            'level' => 'nullable|string|max:50',
        ];
    }
}
