<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePitaPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:dipinjam,kembali,terlambat,menunggu_verifikasi,ditolak',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_kembali' => 'nullable|date',
        ];
    }
}
