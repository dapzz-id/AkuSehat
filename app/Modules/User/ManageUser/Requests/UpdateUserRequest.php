<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('user')?->id ?? $this->id;
        $instansiId = auth()->user()?->instansi_id;

        return [
            'nomor_induk' => [
                'required',
                'string',
                'max:30',
                $instansiId
                    ? Rule::unique('users', 'nomor_induk')->where('instansi_id', $instansiId)->ignore($id)
                    : Rule::unique('users', 'nomor_induk')->ignore($id),
            ],
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'level' => 'nullable|in:Admin,Admin Instansi,Health Consultant,Health Monitor,Member,Siswa,Guru BK,Guru Olahraga,Admin PMR',
            'jk' => 'required|in:L,P',
            'id_divisi' => [
                'nullable',
                $instansiId
                    ? Rule::exists('divisi', 'id')->where('instansi_id', $instansiId)
                    : 'exists:divisi,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_induk.required' => 'Nomor induk / NIS / NIK wajib diisi.',
            'nomor_induk.unique' => 'Nomor induk sudah terdaftar untuk pengguna lain.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ];
    }
}
