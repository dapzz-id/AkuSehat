<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePitaPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'estimasi_selesai_haid' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
