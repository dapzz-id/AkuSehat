<?php

namespace App\Modules\Hb\PemeriksaanHb\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StoreHbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instansiId = auth()->user()?->instansi_id;

        return [
            'id_user' => [
                'required',
                $instansiId
                    ? Rule::exists('users', 'id')->where('instansi_id', $instansiId)
                    : 'exists:users,id',
            ],
            'hb' => 'required|numeric|min:3|max:25',
            'tgl' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Member wajib dipilih.',
            'id_user.exists' => 'Member tidak ditemukan.',
            'hb.required' => 'Kadar HB wajib diisi.',
            'hb.numeric' => 'Kadar HB harus berupa angka.',
            'hb.min' => 'Kadar HB minimal 3 g/dL.',
            'hb.max' => 'Kadar HB maksimal 25 g/dL.',
        ];
    }
}
