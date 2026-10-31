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
            return User::whereRaw('LOWER(email) = ?', [strtolower($login)])->first();
        }

        return User::where('username', $login)->first();
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
