<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $divisiId = $this->route('divisi')?->id ?? $this->route('divisi');

        return [
            'divisi' => 'required|string|max:50|unique:divisi,divisi_name,' . $divisiId,
            'kategori' => 'nullable|string|in:member,staf,pimpinan',
            'cover_color' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'divisi.required' => 'Divisi harus diisi.',
            'divisi.max' => 'Divisi maksimal 50 karakter.',
            'divisi.unique' => 'Nama divisi sudah ada.',
            'cover_color.required' => 'Warna cover harus diisi.',
            'cover_color.max' => 'Warna cover maksimal 20 karakter.',
        ];
    }
}
