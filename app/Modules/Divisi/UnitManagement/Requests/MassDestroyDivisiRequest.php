<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:divisi,id',
        ];
    }
}
