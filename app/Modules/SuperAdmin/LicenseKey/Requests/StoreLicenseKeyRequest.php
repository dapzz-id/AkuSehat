<?php

namespace App\Modules\SuperAdmin\LicenseKey\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\SuperAdmin\LicenseKey\Enums\LicenseStatus;

class StoreLicenseKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:30|unique:license_keys,key',
            'instansi_id' => 'nullable|exists:instansi,id',
            'kuota_pengguna' => 'required|integer|min:1',
            'tanggal_berakhir' => 'required|date|after_or_equal:today',
            'status' => 'required|in:' . implode(',', LicenseStatus::values()),
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'License Key wajib diisi.',
            'key.unique' => 'License Key sudah terdaftar.',
            'kuota_pengguna.required' => 'Kuota pengguna wajib diisi.',
            'tanggal_berakhir.required' => 'Tanggal berakhir wajib diisi.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal berakhir tidak boleh di masa lalu.',
        ];
    }
}
