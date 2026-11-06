<?php

namespace App\Modules\SuperAdmin\LicenseKey\Services;

use App\Models\LicenseKey;
use App\Modules\SuperAdmin\LicenseKey\Enums\LicenseStatus;
use App\Modules\SuperAdmin\LicenseKey\Repositories\LicenseKeyRepository;
use App\Services\AuditLogger;

class LicenseKeyService
{
    public function __construct(
        protected LicenseKeyRepository $repository
    ) {}

    public function getCreateFormData(): array
    {
        $instansi = $this->repository->getAllInstansi();
        $generatedKey = $this->repository->generateNewKeyString();

        return compact('instansi', 'generatedKey');
    }

    public function getEditFormData(int|string $id): array
    {
        $license = $this->repository->findOrFail($id);
        $instansi = $this->repository->getAllInstansi();

        return compact('license', 'instansi');
    }

    public function generateLicense(array $data): LicenseKey
    {
        $license = $this->repository->create([
            'key' => $data['key'],
            'instansi_id' => $data['instansi_id'] ?: null,
            'kuota_pengguna' => $data['kuota_pengguna'],
            'status' => $data['status'],
            'tanggal_berakhir' => $data['tanggal_berakhir'],
        ]);

        $this->repository->syncActiveInstansi($license);

        AuditLogger::log(
            'license_generated',
            'system',
            "SuperAdmin membuat License Key baru: {$license->key} (Kuota: {$license->kuota_pengguna})",
            ['license_id' => $license->id, 'instansi_id' => $license->instansi_id]
        );

        return $license;
    }

    public function updateLicense(int|string $id, array $validated): LicenseKey
    {
        $license = $this->repository->findOrFail($id);

        $status = match($validated['status']) {
            'aktif', LicenseStatus::ACTIVE->value => LicenseStatus::ACTIVE->value,
            'nonaktif', 'expired', LicenseStatus::EXPIRED->value => LicenseStatus::EXPIRED->value,
            LicenseStatus::SUSPENDED->value => LicenseStatus::SUSPENDED->value,
            default => $validated['status']
        };

        $this->repository->update($license, [
            'instansi_id' => $validated['instansi_id'] ?: null,
            'kuota_pengguna' => $validated['kuota_pengguna'],
            'status' => $status,
            'tanggal_berakhir' => $validated['tanggal_berakhir'],
        ]);

        $this->repository->syncActiveInstansi($license);

        AuditLogger::log(
            'license_updated',
            'system',
            "SuperAdmin memperbarui License Key: {$license->key}",
            ['license_id' => $license->id]
        );

        return $license;
    }

    public function renewLicense(int|string $id, array $validated): array
    {
        $license = $this->repository->findOrFail($id);

        $baseDate = $license->tanggal_berakhir && $license->tanggal_berakhir->isFuture() 
            ? $license->tanggal_berakhir 
            : now();

        $newEndDate = $baseDate->copy()->addMonths((int)$validated['durasi_bulan']);

        $this->repository->update($license, [
            'tanggal_berakhir' => $newEndDate,
            'status' => LicenseKey::STATUS_ACTIVE,
        ]);

        $this->repository->syncActiveInstansi($license);

        AuditLogger::log(
            'license_renewed',
            'system',
            "SuperAdmin memperpanjang License Key: {$license->key} hingga {$newEndDate->format('Y-m-d')}",
            ['license_id' => $license->id, 'new_end_date' => $newEndDate->toDateString()]
        );

        return [
            'license' => $license,
            'new_end_date' => $newEndDate,
        ];
    }

    public function canDeleteLicense(LicenseKey $license): array
    {
        $instansi = $license->sekolah;

        if (!$instansi) {
            return [
                'can_delete' => true,
                'action_type' => 'direct',
                'reason' => null,
                'used_quota' => 0,
                'total_users' => 0,
                'other_quota' => 0,
                'target_license' => null,
            ];
        }

        $totalUsers = $instansi->getCountableUsersCount();

        // Cari lisensi aktif lain di instansi ini yang belum expired
        $otherActiveLicenses = LicenseKey::where('instansi_id', $instansi->id)
            ->where('id', '!=', $license->id)
            ->where('status', LicenseKey::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->orderBy('tanggal_berakhir', 'desc')
            ->get();

        $otherQuota = (int) $otherActiveLicenses->sum('kuota_pengguna');
        $usedQuota = $license->getUsedQuota();

        // Jika lisensi sudah tidak aktif/expired, lisensi dapat dibersihkan/dihapus
        if (!$license->isActive()) {
            return [
                'can_delete' => true,
                'action_type' => 'direct',
                'reason' => null,
                'used_quota' => 0,
                'total_users' => $totalUsers,
                'other_quota' => $otherQuota,
                'target_license' => $otherActiveLicenses->first(),
            ];
        }

        // Jika lisensi aktif, tapi tidak ada anggota terdaftar sama sekali
        if ($totalUsers === 0) {
            return [
                'can_delete' => true,
                'action_type' => 'direct',
                'reason' => null,
                'used_quota' => 0,
                'total_users' => 0,
                'other_quota' => $otherQuota,
                'target_license' => $otherActiveLicenses->first(),
            ];
        }

        // Lisensi aktif dan sedang menampung pengguna ($totalUsers > 0)
        // Kasus 1: Tidak ada lisensi aktif cadangan lain
        if ($otherActiveLicenses->isEmpty()) {
            return [
                'can_delete' => false,
                'action_type' => 'blocked_in_use',
                'reason' => "Lisensi [{$license->key}] tidak dapat dihapus karena sedang aktif digunakan oleh {$instansi->nama_instansi} ({$totalUsers} pengguna aktif) dan tidak ada lisensi cadangan lain.",
                'used_quota' => $usedQuota,
                'total_users' => $totalUsers,
                'other_quota' => 0,
                'target_license' => null,
            ];
        }

        // Kasus 2: Ada lisensi aktif lain, tapi total kuota lisensi lain tidak mencukupi seluruh pengguna instansi
        if ($otherQuota < $totalUsers) {
            $defisit = $totalUsers - $otherQuota;
            return [
                'can_delete' => false,
                'action_type' => 'blocked_overflow',
                'reason' => "Lisensi [{$license->key}] tidak dapat dihapus. Instansi {$instansi->nama_instansi} memiliki {$totalUsers} pengguna aktif, sedangkan kapasitas lisensi aktif lainnya hanya menampung {$otherQuota} pengguna (kurang {$defisit} kuota).",
                'used_quota' => $usedQuota,
                'total_users' => $totalUsers,
                'other_quota' => $otherQuota,
                'target_license' => $otherActiveLicenses->first(),
            ];
        }

        // Kasus 3: Ada lisensi aktif lain dan kuota lisensi lain mencukupi ($otherQuota >= $totalUsers)
        $targetLicense = $otherActiveLicenses->first();
        return [
            'can_delete' => true,
            'action_type' => 'transfer',
            'reason' => null,
            'used_quota' => $usedQuota,
            'total_users' => $totalUsers,
            'other_quota' => $otherQuota,
            'target_license' => $targetLicense,
        ];
    }

    public function deleteLicense(int|string $id): array
    {
        $license = $this->repository->findOrFail($id);
        $check = $this->canDeleteLicense($license);

        if (!$check['can_delete']) {
            throw new \Exception($check['reason']);
        }

        $instansi = $license->sekolah;
        $transferredTo = null;

        if ($instansi && $check['action_type'] === 'transfer' && $check['target_license']) {
            $transferredTo = $check['target_license'];
            $instansi->update(['license_id_active' => $transferredTo->id]);
        } elseif ($instansi && $instansi->license_id_active === $license->id) {
            $nextActive = LicenseKey::where('instansi_id', $instansi->id)
                ->where('id', '!=', $license->id)
                ->where('status', LicenseKey::STATUS_ACTIVE)
                ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
                ->orderBy('tanggal_berakhir', 'desc')
                ->first();
            $instansi->update(['license_id_active' => $nextActive?->id]);
        }

        $key = $license->key;
        $this->repository->delete($license);

        $msg = "License Key [{$key}] berhasil dihapus.";
        if ($transferredTo) {
            $msg = "License Key [{$key}] berhasil dihapus. Kuota {$check['total_users']} pengguna otomatis dialihkan ke lisensi aktif [{$transferredTo->key}].";
        }

        AuditLogger::log(
            'license_deleted',
            'system',
            "SuperAdmin menghapus License Key: {$key}" . ($transferredTo ? " (Dialihkan ke {$transferredTo->key})" : ""),
            ['license_id' => $id, 'transferred_to' => $transferredTo?->id]
        );

        return [
            'success' => true,
            'message' => $msg,
        ];
    }

    public function revokeLicense(LicenseKey $license): void
    {
        $this->repository->delete($license);

        AuditLogger::log(
            'license_revoked',
            'system',
            "SuperAdmin mencabut License Key: {$license->key}",
            ['license_id' => $license->id]
        );
    }
}
