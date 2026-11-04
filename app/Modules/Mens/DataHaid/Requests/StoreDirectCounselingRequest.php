<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDirectCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $instansiId = auth()->user()?->instansi_id;

        return [
            'user_id' => [
                'required',
                $instansiId
                    ? Rule::exists('users', 'id')->where('instansi_id', $instansiId)
                    : 'exists:users,id',
            ],
            'topic' => 'required|string|max:150',
            'session_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Siswa/siswi wajib dipilih.',
            'user_id.exists' => 'Siswa/siswi tidak ditemukan atau tidak berada di instansi Anda.',
            'topic.required' => 'Topik bimbingan/konseling wajib diisi.',
            'session_date.required' => 'Tanggal sesi konseling wajib diisi.',
        ];
    }
}
