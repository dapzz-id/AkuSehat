<?php

namespace App\Modules\Divisi\UnitManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instansiId = auth()->user()?->instansi_id;
        $divisiParam = $this->route('divisi') ?? $this->route('id');
        $divisiId = $divisiParam instanceof \App\Models\Divisi ? $divisiParam->id : ($divisiParam ?? $this->input('id'));

        return [
            'divisi' => [
                'required',
                'string',
                'max:50',
                $instansiId
                    ? Rule::unique('divisi', 'divisi_name')->where('instansi_id', $instansiId)->ignore($divisiId)
                    : Rule::unique('divisi', 'divisi_name')->ignore($divisiId),
            ],
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
