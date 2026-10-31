<?php

namespace App\Modules\Hb\PemeriksaanHb\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportHbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'filter_type' => 'nullable|string|in:all,divisi,tanggal,divisi_tanggal',
            'divisi' => 'nullable|string',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ];
    }
}
