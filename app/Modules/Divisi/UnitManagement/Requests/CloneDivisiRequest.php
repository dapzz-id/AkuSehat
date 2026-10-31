<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloneDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_divisi_name' => 'required|string|max:50',
            'new_cover_color' => 'nullable|string|max:20',
        ];
    }
}
