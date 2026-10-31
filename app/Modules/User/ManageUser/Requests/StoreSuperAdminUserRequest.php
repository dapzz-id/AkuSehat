<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\User\ManageUser\Enums\UserRole;
use App\Modules\User\ManageUser\Enums\UserGender;

class StoreSuperAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('instansi_id') && $this->has('sekolah_id')) {
            $this->merge(['instansi_id' => $this->input('sekolah_id')]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username',
            'email' => 'nullable|email|max:100',
            'password' => 'required|string|min:6',
            'level' => 'required|in:' . implode(',', UserRole::values()),
            'jk' => 'required|in:' . implode(',', UserGender::values()),
            'instansi_id' => 'required|exists:instansi,id',
            'nomor_induk' => 'nullable|string|max:30',
            'nis' => 'nullable|string|max:30',
            'tgl' => 'nullable|date',
            'id_divisi' => 'nullable|exists:divisi,id',
        ];
    }

    public function messages(): array
    {
        return [
            'instansi_id.required' => 'Asal institusi / instansi wajib dipilih.',
            'instansi_id.exists' => 'Institusi / instansi yang dipilih tidak valid.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }
}
