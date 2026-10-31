<?php

namespace App\Modules\SuperAdmin\ModulePermission\Repositories;

use App\Models\GlobalModuleConfig;
use App\Models\User;
use App\Models\Instansi;
use Illuminate\Support\Collection;

class ModulePermissionRepository
{
    public function getAllGlobalConfigs(): Collection
    {
        return GlobalModuleConfig::all()->keyBy('module_key');
    }

    public function getTesters(): Collection
    {
        return User::where('is_tester', true)
            ->select('id', 'nama', 'username', 'email', 'level', 'nomor_induk', 'instansi_id')
            ->with('instansi:id,nama_instansi')
            ->orderBy('nama', 'asc')
            ->get();
    }

    public function searchRecentUsers(?string $search = null, int $limit = 250): Collection
    {
        $query = User::orderBy('nama', 'asc')
            ->select('id', 'nama', 'username', 'email', 'level', 'nomor_induk', 'instansi_id', 'is_tester')
            ->with('instansi:id,nama_instansi');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhereHas('instansi', function ($iq) use ($search) {
                      $iq->where('nama_instansi', 'like', "%{$search}%");
                  });
            });
            return $query->limit(100)->get();
        }

        return $query->limit($limit)->get();
    }

    public function updateOrCreateConfig(string $key, array $data): GlobalModuleConfig
    {
        return GlobalModuleConfig::updateOrCreate(
            ['module_key' => $key],
            $data
        );
    }

    public function updateInstansiModules(Instansi $instansi, array $modules): bool
    {
        return $instansi->update(['active_modules' => $modules]);
    }

    public function updateInstansiConfig(Instansi $instansi, array $attributes): bool
    {
        return $instansi->update($attributes);
    }

    public function toggleTester(int|string $userId): array
    {
        $user = User::findOrFail($userId);
        $user->is_tester = !$user->is_tester;
        $user->save();

        return [
            'user' => $user,
            'is_tester' => (bool) $user->is_tester,
        ];
    }
}
