<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyKesehatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:kesehatan,id_kesehatan',
        ];
    }
}
