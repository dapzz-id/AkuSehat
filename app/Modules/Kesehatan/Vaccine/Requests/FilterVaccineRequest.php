<?php

namespace App\Modules\Kesehatan\Vaccine\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterVaccineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'vaccine_name' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:100',
        ];
    }
}
