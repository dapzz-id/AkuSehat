<?php

namespace App\Modules\Hb\PemeriksaanHb\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterHbRequest extends FormRequest
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
            'status' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:L,P,all',
        ];
    }
}
