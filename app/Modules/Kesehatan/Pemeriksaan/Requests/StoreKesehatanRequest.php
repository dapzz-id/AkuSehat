<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StoreKesehatanRequest extends FormRequest
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
            'bb' => 'required|numeric|min:10|max:300',
            'tb' => 'required|numeric|min:50|max:250',
            'sistol' => 'required|numeric|min:40|max:250',
            'diastol' => 'required|numeric|min:30|max:180',
            'kondisi_telinga' => 'nullable|string|max:100',
            'kondisi_gigi' => 'nullable|string|max:100',
            'perilaku_beresiko' => 'nullable|string|max:255',
            'gangguan_reproduksi' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Pengguna wajib dipilih.',
            'bb.required' => 'Berat badan (BB) wajib diisi.',
            'tb.required' => 'Tinggi badan (TB) wajib diisi.',
            'sistol.required' => 'Tekanan darah sistol wajib diisi.',
            'diastol.required' => 'Tekanan darah diastol wajib diisi.',
        ];
    }
}
