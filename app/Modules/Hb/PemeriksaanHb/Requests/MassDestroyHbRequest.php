<?php

namespace App\Modules\Hb\PemeriksaanHb\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyHbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:hb,id_hb',
        ];
    }
}
