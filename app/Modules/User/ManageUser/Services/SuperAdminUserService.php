<?php

namespace App\Modules\User\ManageUser\Services;

use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;
use App\Modules\User\ManageUser\Repositories\UserRepository;
use App\Modules\User\Enums\UserStatus;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Exception;

class SuperAdminUserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getIndexData(array $filters): array
    {
        $instansi = $this->userRepository->getAllInstansi();
        $sekolah = $instansi;
        $users = $this->userRepository->getSuperAdminFilteredUsers($filters, 15);
        $users->appends($filters);

        return compact('instansi', 'sekolah', 'users');
    }

    public function getCreateFormData(): array
    {
        $instansi = $this->userRepository->getAllInstansi();
        $sekolah = $instansi;
        $divisi = $this->userRepository->getAllDivisions();

        return compact('instansi', 'sekolah', 'divisi');
    }

    public function getEditFormData(int|string $id): array
    {
        $user = $this->userRepository->findOrFail($id);
        $instansi = $this->userRepository->getAllInstansi();
        $sekolah = $instansi;
        $divisi = $this->userRepository->getAllDivisions();

        return compact('user', 'instansi', 'sekolah', 'divisi');
    }

    public function banUser(User $targetUser, ?string $reason, User $actor): void
    {
        if ($targetUser->id === $actor->id) {
            throw new Exception('Tidak dapat mem-banned akun sendiri.');
        }

        if ($targetUser->isSuperAdmin()) {
            throw new Exception('Tidak dapat mem-banned sesama Super Admin.');
        }

        $targetUser->update([
            'status' => UserStatus::BANNED->value,
            'suspended_until' => null,
            'ban_reason' => $reason ?: 'Pelanggaran ketentuan sistem',
        ]);

        $targetUser->tokens()->delete();

        AuditLogger::userAction(
            'user_banned',
            "SuperAdmin mem-banned permanen pengguna: {$targetUser->nama} (@{$targetUser->username})",
            ['user_id' => $targetUser->id, 'reason' => $targetUser->ban_reason]
        );
    }

    public function banUserById(int|string $id, ?string $reason, User $actor): void
    {
        $targetUser = $this->userRepository->findOrFail($id);
        $this->banUser($targetUser, $reason, $actor);
    }

    public function suspendUser(User $targetUser, string $duration, ?string $customDate, ?string $reason, User $actor): void
    {
        if ($targetUser->id === $actor->id) {
            throw new Exception('Tidak dapat men-suspend akun sendiri.');
        }

        if ($targetUser->isSuperAdmin()) {
            throw new Exception('Tidak dapat men-suspend sesama Super Admin.');
        }

        $suspendedUntil = null;
        if ($duration === 'custom') {
            if (empty($customDate)) {
                throw new Exception('Tanggal berakhir suspend wajib diisi.');
            }
            $suspendedUntil = Carbon::parse($customDate);
            if ($suspendedUntil->isPast()) {
                throw new Exception('Waktu suspend harus berada di masa depan.');
            }
        } else {
            $days = (int) $duration;
            $suspendedUntil = now()->addDays($days);
        }

        $targetUser->update([
            'status' => UserStatus::SUSPENDED->value,
            'suspended_until' => $suspendedUntil,
            'ban_reason' => $reason ?: "Ditangguhkan sementara ({$duration} hari)",
        ]);

        $targetUser->tokens()->delete();

        AuditLogger::userAction(
            'user_suspended',
            "SuperAdmin men-suspend akun pengguna: {$targetUser->nama} (@{$targetUser->username}) hingga {$suspendedUntil->format('d/m/Y H:i')}",
            ['user_id' => $targetUser->id, 'suspended_until' => $suspendedUntil->toIso8601String()]
        );
    }

    public function suspendUserById(int|string $id, string $duration, ?string $customDate, ?string $reason, User $actor): void
    {
        $targetUser = $this->userRepository->findOrFail($id);
        $this->suspendUser($targetUser, $duration, $customDate, $reason, $actor);
    }

    public function activateUser(User $targetUser, User $actor): void
    {
        $targetUser->update([
            'status' => UserStatus::AKTIF->value,
            'suspended_until' => null,
            'ban_reason' => null,
        ]);

        AuditLogger::userAction(
            'user_activated',
            "SuperAdmin mengaktifkan kembali akun pengguna: {$targetUser->nama} (@{$targetUser->username})",
            ['user_id' => $targetUser->id]
        );
    }

    public function activateUserById(int|string $id, User $actor): void
    {
        $targetUser = $this->userRepository->findOrFail($id);
        $this->activateUser($targetUser, $actor);
    }

    public function createUser(array $validated): User
    {
        $user = $this->userRepository->create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => !empty($validated['email']) ? $validated['email'] : null,
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'jk' => $validated['jk'],
            'instansi_id' => $validated['instansi_id'],
            'nomor_induk' => !empty($validated['nomor_induk']) ? $validated['nomor_induk'] : (!empty($validated['nis']) ? $validated['nis'] : null),
            'id_divisi' => !empty($validated['id_divisi']) ? $validated['id_divisi'] : null,
            'status' => UserStatus::AKTIF->value,
            'tgl' => !empty($validated['tgl']) ? $validated['tgl'] : now()->format('Y-m-d'),
        ]);

        AuditLogger::userAction(
            'superadmin_create_user',
            "SuperAdmin membuat user: {$user->nama} ({$user->level}) di instansi ID {$user->instansi_id}",
            ['user_id' => $user->id, 'instansi_id' => $user->instansi_id]
        );

        return $user;
    }

    public function updateUser(User $user, array $validated): User
    {
        $updateData = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => !empty($validated['email']) ? $validated['email'] : null,
            'level' => $validated['level'],
            'jk' => $validated['jk'],
            'instansi_id' => $validated['instansi_id'],
            'nomor_induk' => !empty($validated['nomor_induk']) ? $validated['nomor_induk'] : (!empty($validated['nis']) ? $validated['nis'] : null),
            'id_divisi' => !empty($validated['id_divisi']) ? $validated['id_divisi'] : null,
        ];

        if (!empty($validated['tgl'])) {
            $updateData['tgl'] = $validated['tgl'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $this->userRepository->update($user, $updateData);

        AuditLogger::userAction(
            'superadmin_update_user',
            "SuperAdmin mengedit user: {$user->nama} ({$user->level})",
            ['user_id' => $user->id, 'instansi_id' => $user->instansi_id]
        );

        return $user;
    }

    public function updateUserById(int|string $id, array $validated): User
    {
        $user = $this->userRepository->findOrFail($id);
        return $this->updateUser($user, $validated);
    }

    public function deleteUser(User $user): void
    {
        $id = $user->id;
        $nama = $user->nama;
        $this->userRepository->deleteWithRelations($user);

        AuditLogger::userAction(
            'superadmin_delete_user',
            "SuperAdmin menghapus user: {$nama}",
            ['deleted_user_id' => $id]
        );
    }

    public function deleteUserById(int|string $id): void
    {
        $user = $this->userRepository->findOrFail($id);
        $this->deleteUser($user);
    }
}
