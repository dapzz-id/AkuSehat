<?php

namespace App\Modules\SuperAdmin\LicenseKey\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewLicenseKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'durasi_bulan' => 'required|integer|min:1|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'durasi_bulan.required' => 'Durasi perpanjangan lisensi wajib diisi.',
            'durasi_bulan.min' => 'Durasi perpanjangan minimal 1 bulan.',
            'durasi_bulan.max' => 'Durasi perpanjangan maksimal 60 bulan.',
        ];
    }
}
