<?php

namespace App\Modules\Kesehatan\VitalLogs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWaterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => 'nullable|integer|min:-5000|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Jumlah air tidak valid (minimal -5000 ml).',
            'amount.max' => 'Jumlah air tidak valid (maksimal 5000 ml).',
            'amount.integer' => 'Jumlah air harus berupa angka bulat.',
        ];
    }
}
