<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'jumlah_pita' => ['nullable', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password akun wajib dimasukkan untuk konfirmasi pengajuan pinjam pita.',
            'tanggal_pinjam.required' => 'Tanggal pinjam pita wajib diisi.',
        ];
    }
}
