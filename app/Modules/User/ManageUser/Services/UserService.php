<?php

namespace App\Modules\User\ManageUser\Services;

use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;
use App\Models\LicenseKey;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\ManageUser\Repositories\UserRepository;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getIndexData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;
        $currentInstansi = $actor->instansi;
        $isPendidikan = $currentInstansi ? $currentInstansi->isInstitusiPendidikan() : true;

        $activeTab = $filters['tab'] ?? ($isPendidikan ? 'siswa' : 'member');
        $divisiList = $this->userRepository->getDivisionsForInstansi($instansiId);

        $mergedFilters = array_merge($filters, ['tab' => $activeTab]);
        $users = $this->userRepository->getFilteredUsers($instansiId, $mergedFilters, 15);
        $stats = $this->userRepository->getUserStats($instansiId);

        $counts = $stats['counts'];
        $userStats = is_array($stats['userStats']) ? (object) $stats['userStats'] : $stats['userStats'];
        $member = $users;
        $divisi = $divisiList;
        $kategoriOptions = Divisi::getKategoriOptions($currentInstansi?->jenis_instansi);

        return compact(
            'users', 'divisiList', 'activeTab', 'isPendidikan', 'counts', 'userStats',
            'member', 'divisi', 'kategoriOptions'
        );
    }

    public function getCreateFormData(User $actor): array
    {
        $divisi = $this->userRepository->getDivisionsForInstansi((string) $actor->instansi_id);
        return compact('divisi');
    }

    public function getEditFormData(int|string $id, User $actor): array
    {
        $member = $this->userRepository->findOrFail($id);
        if ($member->instansi_id !== $actor->instansi_id && !$actor->isSuperAdmin()) {
            abort(404);
        }

        $divisi = $this->userRepository->getDivisionsForInstansi((string) $actor->instansi_id);
        return compact('member', 'divisi');
    }

    public function massDeleteUsers(array $ids, User $actor): int
    {
        $currentUserId = $actor->id;
        $safeIds = array_diff($ids, [$currentUserId]);

        $count = $this->userRepository->massDeleteUsers((string) $actor->instansi_id, $safeIds);

        AuditLogger::userAction(
            'mass_delete_users',
            "Admin menghapus secara massal {$count} pengguna",
            ['ids' => $safeIds, 'count' => $count]
        );

        return $count;
    }

    public function massUpdateDivisi(array $ids, string $targetDivisiId, User $actor): int
    {
        $count = $this->userRepository->massUpdateDivisi((string) $actor->instansi_id, $ids, $targetDivisiId);

        AuditLogger::userAction(
            'mass_update_divisi',
            "Admin memindahkan {$count} pengguna ke divisi ID: {$targetDivisiId}",
            ['ids' => $ids, 'target_divisi_id' => $targetDivisiId]
        );

        return $count;
    }

    public function massRemoveDivisi(array $ids, User $actor): int
    {
        $count = $this->userRepository->massUpdateDivisi((string) $actor->instansi_id, $ids, null);

        AuditLogger::userAction(
            'mass_remove_divisi',
            "Admin mengeluarkan {$count} pengguna dari divisi",
            ['ids' => $ids]
        );

        return $count;
    }

    public function updateUserPermissions(User $user, bool $canAddUser, User $actor): bool
    {
        if ($user->instansi_id !== $actor->instansi_id && !$actor->isSuperAdmin()) {
            abort(404);
        }

        $updated = $this->userRepository->update($user, [
            'can_add_user' => $canAddUser,
        ]);

        AuditLogger::userAction(
            'update_user_permissions',
            "Admin memperbarui izin can_add_user untuk {$user->nama} menjadi " . ($canAddUser ? 'true' : 'false'),
            ['user_id' => $user->id, 'can_add_user' => $canAddUser]
        );

        return $updated;
    }

    public function createUser(array $data, User $actor): User
    {
        $instansi = $actor->sekolah ?: Instansi::find($actor->instansi_id);
        if (!$instansi) {
            throw new Exception('Instansi Anda belum terdaftar.');
        }

        if (!$instansi->hasActiveLicense()) {
            throw new Exception('Paket lisensi institusi Anda telah kedaluwarsa. Kuota berada pada batas default 1 (Khusus Admin Instansi). Silakan lakukan pembelian atau perpanjangan lisensi di menu Billing.');
        }

        return DB::transaction(function () use ($data, $actor, $instansi) {
            $currentUsersInDivisi = $this->userRepository->countUsersInDivisi($actor->instansi_id);
            $kuotaMaksimal = $instansi->getTotalActiveQuota();

            if ($currentUsersInDivisi >= $kuotaMaksimal) {
                throw new Exception("Kuota lisensi institusi Anda ({$kuotaMaksimal} slot) sudah penuh.");
            }

            $user = $this->userRepository->create([
                'nomor_induk' => $data['nomor_induk'],
                'nama' => $data['nama'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'id_divisi' => $data['id_divisi'],
                'instansi_id' => $actor->instansi_id,
                'jk' => $data['jk'],
                'level' => $data['level'] ?? UserRole::MEMBER->value,
                'tgl' => now()->format('Y-m-d'),
                'status' => UserStatus::AKTIF->value,
            ]);

            AuditLogger::userAction(
                'user_create',
                "Admin membuat pengguna baru: {$user->nama} ({$user->level}) di divisi ID: {$user->id_divisi}",
                ['user_id' => $user->id, 'instansi_id' => $user->instansi_id]
            );

            return $user;
        });
    }

    public function updateUser(User $targetUser, array $data, User $actor): User
    {
        if ($targetUser->id === $actor->id) {
            if (!empty($data['level']) && $data['level'] !== $targetUser->level) {
                throw new Exception('Anda tidak dapat mengubah peran (role) akun Anda sendiri demi keamanan.');
            }
        }

        if ($targetUser->instansi_id !== $actor->instansi_id && !$actor->isSuperAdmin()) {
            throw new Exception('Anda tidak memiliki akses untuk mengubah data pengguna dari instansi lain.');
        }

        $instansi = $actor->sekolah ?: Instansi::find($actor->instansi_id);

        if (!$actor->isSuperAdmin() && (!$instansi || !$instansi->hasActiveLicense())) {
            throw new Exception('Paket lisensi aktif pada instansi belum ditemukan atau sudah kedaluwarsa.');
        }

        $newDivisiId = !empty($data['id_divisi']) ? (string) $data['id_divisi'] : null;

        if ($targetUser->id_divisi === null && $newDivisiId !== null && $instansi) {
            $currentUsersInDivisi = $this->userRepository->countUsersInDivisi($actor->instansi_id);
            $kuotaMaksimal = $instansi->getTotalActiveQuota();
            if ($currentUsersInDivisi >= $kuotaMaksimal) {
                throw new Exception("Kuota lisensi instansi Anda ({$kuotaMaksimal} slot) sudah penuh.");
            }
        }

        $updateData = [
            'nomor_induk' => $data['nomor_induk'],
            'nama' => $data['nama'],
            'username' => $data['username'],
            'jk' => $data['jk'],
            'id_divisi' => $newDivisiId,
        ];

        if (!empty($data['level'])) {
            $updateData['level'] = $data['level'];
        }

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $this->userRepository->update($targetUser, $updateData);

        AuditLogger::userAction(
            'user_update',
            "Admin memperbarui data pengguna: {$targetUser->nama} (@{$targetUser->username})",
            ['user_id' => $targetUser->id]
        );

        return $targetUser;
    }

    public function updateUserById(int|string $id, array $data, User $actor): User
    {
        $targetUser = $this->userRepository->findOrFail($id);
        return $this->updateUser($targetUser, $data, $actor);
    }

    public function deleteUser(User $targetUser, User $actor): void
    {
        if ($targetUser->id === $actor->id) {
            throw new Exception('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($targetUser->instansi_id !== $actor->instansi_id && !$actor->isSuperAdmin()) {
            throw new Exception('Anda tidak memiliki izin menghapus pengguna instansi lain.');
        }

        $nama = $targetUser->nama;
        $username = $targetUser->username;
        $userId = $targetUser->id;

        $this->userRepository->deleteWithRelations($targetUser);

        AuditLogger::userAction(
            'user_delete',
            "Admin menghapus akun pengguna: {$nama} (@{$username})",
            ['user_id' => $userId]
        );
    }

    public function deleteUserById(int|string $id, User $actor): void
    {
        $targetUser = $this->userRepository->findOrFail($id);
        $this->deleteUser($targetUser, $actor);
    }

    public function getMembersForApi(User $actor): array
    {
        if (!$actor->isHealthConsultant() && !$actor->isSuperAdmin() && !$actor->isAdminInstansi()) {
            return ['status' => false, 'code' => 403, 'message' => 'Akses tidak diperbolehkan.'];
        }

        $members = $this->userRepository->getMembersByInstansi($actor->instansi_id, 15);

        return ['status' => true, 'code' => 200, 'data' => $members];
    }

    public function getMembersByDivisiForApi(User $actor, int|string $divisiId): array
    {
        if (!$actor->isHealthConsultant() && !$actor->isSuperAdmin() && !$actor->isAdminInstansi()) {
            return ['status' => false, 'code' => 403, 'message' => 'Akses tidak diperbolehkan.'];
        }

        $members = $this->userRepository->getMembersByDivisi($actor->instansi_id, $divisiId);

        return ['status' => true, 'code' => 200, 'data' => $members];
    }
}
