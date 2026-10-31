<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:100',
        ];
    }
}
