<?php

namespace App\Modules\SuperAdmin\Instansi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetKabupatenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'province_id' => 'required|regex:/^[0-9]+$/',
        ];
    }
}
