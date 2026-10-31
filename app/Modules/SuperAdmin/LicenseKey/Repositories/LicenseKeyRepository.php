<?php

namespace App\Modules\SuperAdmin\LicenseKey\Repositories;

use App\Models\LicenseKey;
use App\Models\Instansi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LicenseKeyRepository
{
    public function getPaginatedWithSekolah(int $perPage = 10): LengthAwarePaginator
    {
        return LicenseKey::with('sekolah')->latest()->paginate($perPage);
    }

    public function findOrFail(int|string $id): LicenseKey
    {
        return LicenseKey::with('sekolah')->findOrFail($id);
    }

    public function getAllInstansi(): Collection
    {
        return Instansi::orderBy('nama_instansi')->get();
    }

    public function generateNewKeyString(): string
    {
        return LicenseKey::generateKey();
    }

    public function create(array $data): LicenseKey
    {
        return LicenseKey::create($data);
    }

    public function update(LicenseKey $license, array $data): bool
    {
        return $license->update($data);
    }

    public function delete(LicenseKey $license): ?bool
    {
        Instansi::where('license_id_active', $license->id)->update(['license_id_active' => null]);
        return $license->delete();
    }

    public function syncActiveInstansi(LicenseKey $license): void
    {
        if ($license->instansi_id && $license->status === LicenseKey::STATUS_ACTIVE) {
            Instansi::where('id', $license->instansi_id)->update(['license_id_active' => $license->id]);
        }
    }
}
