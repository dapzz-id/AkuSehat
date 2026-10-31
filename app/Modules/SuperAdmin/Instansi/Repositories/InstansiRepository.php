<?php

namespace App\Modules\SuperAdmin\Instansi\Repositories;

use App\Models\Instansi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InstansiRepository
{
    public function getPaginatedWithLicense(int $perPage = 10): LengthAwarePaginator
    {
        return Instansi::with(['licenseKey'])->latest()->paginate($perPage);
    }

    public function getAllOrderedByName(): Collection
    {
        return Instansi::orderBy('nama_instansi')->get();
    }

    public function findById(int|string $id): ?Instansi
    {
        return Instansi::find($id);
    }

    public function findOrFail(int|string $id): Instansi
    {
        return Instansi::findOrFail($id);
    }

    public function create(array $data): Instansi
    {
        return Instansi::create($data);
    }

    public function update(Instansi $instansi, array $data): bool
    {
        return $instansi->update($data);
    }

    public function deleteWithUsers(Instansi $instansi): bool
    {
        if ($instansi->users()->exists()) {
            $instansi->users()->delete();
        }

        return $instansi->delete();
    }
}
