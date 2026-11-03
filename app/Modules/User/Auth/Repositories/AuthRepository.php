<?php

namespace App\Modules\User\Auth\Repositories;

use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;

class AuthRepository
{
    public function findByLogin(string $login): ?User
    {
        $login = trim($login);
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType === 'email') {
            return User::withoutGlobalScopes()
                ->whereRaw('LOWER(email) = ?', [strtolower($login)])
                ->first();
        }

        // 1. Prioritaskan pencarian berdasarkan username unik global
        $userByUsername = User::withoutGlobalScopes()
            ->whereRaw('LOWER(username) = ?', [strtolower($login)])
            ->first();

        if ($userByUsername) {
            return $userByUsername;
        }

        // 2. Jika bukan username, cari berdasarkan nomor_induk.
        // PENCEGAHAN IDOR & KEBOCORAN AKUN LINTAS INSTANSI:
        // Karena nomor_induk boleh sama antar instansi yang berbeda,
        // hanya izinkan login jika ditemukan TEPAT 1 akun di seluruh sistem.
        // Jika terdapat 2 atau lebih pengguna dengan nomor_induk yang sama di instansi berbeda,
        // sistem menolak login acak demi keamanan IDOR (pengguna harus login menggunakan username unik atau email).
        $candidates = User::withoutGlobalScopes()
            ->where('nomor_induk', $login)
            ->get();

        if ($candidates->count() === 1) {
            return $candidates->first();
        }

        return null;
    }

    public function findByGoogleIdOrEmail(string $googleId, string $email): ?User
    {
        $normalizedEmail = strtolower(trim($email));
        return User::where('google_id', $googleId)
            ->orWhereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();
    }

    public function updateGoogleInfo(User $user, string $googleId, ?string $avatar = null): User
    {
        if (empty($user->google_id)) {
            $user->google_id = $googleId;
        }
        if ($avatar && empty($user->avatar)) {
            $user->avatar = $avatar;
        }
        $user->save();
        return $user;
    }

    public function findInstansiByInviteCodeOrNpsn(string $code): ?Instansi
    {
        $code = trim($code);
        return Instansi::where('invite_code', $code)
            ->orWhere('npsn', $code)
            ->first();
    }

    public function countUsersInInstansi(string|int $instansiId): int
    {
        return User::where('instansi_id', $instansiId)->count();
    }

    public function linkUserToInstansi(User $user, string|int $instansiId): void
    {
        $user->update([
            'instansi_id' => $instansiId,
        ]);
    }
}
