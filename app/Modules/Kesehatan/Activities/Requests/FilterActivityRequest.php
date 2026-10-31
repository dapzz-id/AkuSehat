<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'activity_type' => 'nullable|string|max:50',
            'search' => 'nullable|string|max:100',
        ];
    }
}
