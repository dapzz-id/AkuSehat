<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataHaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'estimasi_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keluhan' => 'nullable|string|max:255',
            'status' => 'nullable|in:berlangsung,selesai',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Pengguna wajib dipilih.',
            'id_user.exists' => 'Pengguna tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai haid wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai haid tidak valid.',
            'estimasi_selesai.after_or_equal' => 'Estimasi selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
