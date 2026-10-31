<?php

namespace App\Modules\Kesehatan\Ergonomics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterErgonomicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'search' => 'nullable|string|max:100',
        ];
    }
}
