<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectPitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ];
    }
}
