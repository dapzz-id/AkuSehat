<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterKesehatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:10',
            'divisi' => 'nullable|string',
            'status_gizi' => 'nullable|string|max:50',
            'status_tensi' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:L,P,all',
        ];
    }
}
