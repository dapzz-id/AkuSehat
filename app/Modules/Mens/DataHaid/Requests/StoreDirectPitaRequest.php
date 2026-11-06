<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\User\Enums\UserGender;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Modules\Mens\DataHaid\Enums\PitaStatus;
use App\Models\DataHaid;
use App\Models\PeminjamanPita;

class StoreDirectPitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdminInstansi() || auth()->user()->canCreate('haid_pita', 'duty'));
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
                    // 1. Anti-IDOR & Syarat Utama: Siswi wajib sedang dalam siklus haid aktif
                    $activeHaid = DataHaid::where('id_user', $value)
                        ->when($instansiId, fn($q) => $q->where('instansi_id', $instansiId))
                        ->where(function ($q) {
                            $q->where('status', HaidStatus::BERLANGSUNG->value)
                              ->orWhereNull('tanggal_selesai');
                        })
                        ->exists();

                    if (!$activeHaid) {
                        $fail('Siswi belum memulai siklus haid aktif. Harap catat "Mulai Haid Siswi" terlebih dahulu sebelum meminjam pita UKS.');
                        return;
                    }

                    // 2. Syarat Integritas: Siswi tidak boleh memiliki pita yang belum dikembalikan
                    $activePita = PeminjamanPita::where('id_user', $value)
                        ->when($instansiId, fn($q) => $q->where('instansi_id', $instansiId))
                        ->whereIn('status', [
                            PitaStatus::DIPINJAM->value,
                            PitaStatus::TERLAMBAT->value,
                            PitaStatus::MENUNGGU->value,
                            PitaStatus::MENUNGGU_VERIFIKASI->value,
                        ])
                        ->whereNull('tanggal_kembali')
                        ->exists();

                    if ($activePita) {
                        $fail('Siswi ini masih memiliki peminjaman pita yang belum dikembalikan.');
                    }
                },
            ],
            'tanggal_pinjam' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Siswi wajib dipilih.',
            'id_user.exists' => 'Siswi tidak valid, tidak berjenis kelamin perempuan, atau tidak berada di instansi Anda.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.date' => 'Format tanggal pinjam tidak valid.',
        ];
    }
}
