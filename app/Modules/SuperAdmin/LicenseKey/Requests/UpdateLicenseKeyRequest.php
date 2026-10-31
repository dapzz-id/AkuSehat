<?php

namespace App\Modules\SuperAdmin\LicenseKey\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenseKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'instansi_id' => 'nullable|exists:instansi,id',
            'kuota_pengguna' => 'required|integer|min:1',
            'tanggal_berakhir' => 'required|date',
            'status' => 'required|in:active,expired,aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kuota_pengguna.required' => 'Kuota pengguna wajib diisi.',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi.',
        ];
    }
}
