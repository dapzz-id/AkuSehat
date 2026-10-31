<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi' => 'required|string|max:50',
            'kategori' => 'nullable|string|in:member,staf,pimpinan',
            'cover_color' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'divisi.required' => 'Nama divisi wajib diisi.',
            'divisi.max' => 'Nama divisi maksimal 50 karakter.',
            'cover_color.required' => 'Warna cover wajib dipilih.',
        ];
    }
}
