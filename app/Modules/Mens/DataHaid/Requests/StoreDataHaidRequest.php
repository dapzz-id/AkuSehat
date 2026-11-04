<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\User\Enums\UserGender;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Models\DataHaid;

class StoreDataHaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $instansiId = auth()->user()?->instansi_id;

        return [
            'id_user' => [
                'required',
                $instansiId
                    ? Rule::exists('users', 'id')
                        ->where('instansi_id', $instansiId)
                        ->where('jk', UserGender::PEREMPUAN->value)
                    : 'exists:users,id',
                function ($attribute, $value, $fail) use ($instansiId) {
                    $activeHaid = DataHaid::where('id_user', $value)
                        ->when($instansiId, fn($q) => $q->where('instansi_id', $instansiId))
                        ->where(function ($q) {
                            $q->where('status', HaidStatus::BERLANGSUNG->value)
                              ->orWhereNull('tanggal_selesai');
                        })
                        ->exists();

                    if ($activeHaid) {
                        $fail('Siswi ini sudah memiliki siklus menstruasi yang sedang berlangsung aktif.');
                    }
                },
            ],
            'tanggal_mulai' => 'required|date',
            'estimasi_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keluhan' => 'nullable|string|max:255',
            'status' => 'nullable|in:berlangsung,selesai',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Siswi wajib dipilih.',
            'id_user.exists' => 'Siswi tidak valid, tidak berjenis kelamin perempuan, atau tidak berada di instansi Anda.',
            'tanggal_mulai.required' => 'Tanggal mulai haid wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai haid tidak valid.',
            'estimasi_selesai.after_or_equal' => 'Estimasi selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
