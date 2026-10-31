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

    public function deleteLicense(int|string $id): void
    {
        $license = $this->repository->findOrFail($id);
        $this->repository->delete($license);

        AuditLogger::log(
            'license_deleted',
            'system',
            "SuperAdmin menghapus License Key: {$license->key}",
            ['license_id' => $id]
        );
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
