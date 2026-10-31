<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'level' => 'nullable|string',
            'instansi_id' => 'nullable|string',
            'sekolah_id' => 'nullable|string',
            'status' => 'nullable|string',
            'search' => 'nullable|string|max:100',
        ];
    }
}
