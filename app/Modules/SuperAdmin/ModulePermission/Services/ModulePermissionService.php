<?php

namespace App\Modules\SuperAdmin\ModulePermission\Services;

use App\Models\GlobalModuleConfig;
use App\Models\Instansi;
use App\Models\User;
use App\Modules\SuperAdmin\ModulePermission\Repositories\ModulePermissionRepository;
use App\Services\AuditLogger;

class ModulePermissionService
{
    public function __construct(
        protected ModulePermissionRepository $repository
    ) {}

    public function getSuperAdminDashboardData(?string $testerSearch = null): array
    {
        $catalog = GlobalModuleConfig::getModuleCatalog();
        $configs = $this->repository->getAllGlobalConfigs();
        $testers = $this->repository->getTesters();
        $recentUsers = $this->repository->searchRecentUsers($testerSearch);

        return compact('catalog', 'configs', 'testers', 'recentUsers', 'testerSearch');
    }

    public function getInstansiModulesData(User $user): ?array
    {
        $instansi = $user->instansi;
        if (!$instansi) {
            return null;
        }

        $availableModules = GlobalModuleConfig::getModuleCatalog();
        $activeModules = $instansi->active_modules ?? array_keys($availableModules);
        $roleLabels = $instansi->custom_role_labels ?? Instansi::getDefaultRolePresets($instansi->jenis_instansi ?? 'Sekolah');
        $permissions = $instansi->module_permissions ?? [];
        $globalConfigs = $this->repository->getAllGlobalConfigs();

        return compact('instansi', 'availableModules', 'activeModules', 'roleLabels', 'permissions', 'globalConfigs');
    }

    public function updateGlobalModules(array $modulePayloads): array
    {
        $catalog = GlobalModuleConfig::getModuleCatalog();
        $changes = [];

        foreach ($modulePayloads as $key => $data) {
            if (!array_key_exists($key, $catalog)) {
                continue;
            }

            $moduleName = $catalog[$key]['name'] ?? $key;
            $testerIds = !empty($data['tester_user_ids']) ? (array) $data['tester_user_ids'] : [];

            $this->repository->updateOrCreateConfig($key, [
                'status' => $data['status'],
                'maintenance_message' => $data['maintenance_message'] ?? null,
                'tester_user_ids' => $testerIds,
            ]);

            $changes[] = "{$moduleName} -> {$data['status']}";
        }

        GlobalModuleConfig::clearCache();

        // Webhook-style event invalidation: bump global modules version
        \Illuminate\Support\Facades\Cache::forever(
            'global_modules_version',
            (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6)
        );

        AuditLogger::module(
            'global_modules_updated',
            'SuperAdmin memperbarui status akses fitur global: ' . implode(', ', $changes),
            ['changes' => $changes]
        );

        return $changes;
    }

    public function updateInstansiModules(Instansi $instansi, array $activeModules): void
    {
        $this->repository->updateInstansiModules($instansi, $activeModules);

        // Webhook-style event invalidation: bump instansi modules version
        \Illuminate\Support\Facades\Cache::forever(
            "instansi_modules_version_{$instansi->id}",
            (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6)
        );

        AuditLogger::module(
            'instansi_modules_updated',
            "Pengaturan modul instansi {$instansi->nama_instansi} diperbarui",
            ['active_modules' => $activeModules]
        );
    }

    public function updateInstansiConfig(Instansi $instansi, array $activeModules, array $roleLabels = [], array $permissions = []): void
    {
        $this->repository->updateInstansiConfig($instansi, [
            'active_modules' => $activeModules,
            'custom_role_labels' => $roleLabels,
            'module_permissions' => $permissions,
        ]);

        // Webhook-style event invalidation: bump instansi modules version
        \Illuminate\Support\Facades\Cache::forever(
            "instansi_modules_version_{$instansi->id}",
            (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6)
        );

        AuditLogger::module(
            'instansi_modules_updated',
            "Pengaturan modul dan hak akses instansi {$instansi->nama_instansi} diperbarui",
            [
                'active_modules' => $activeModules,
                'custom_role_labels' => $roleLabels,
                'module_permissions' => $permissions,
            ]
        );
    }

    public function toggleTesterStatus(int|string $userId): array
    {
        $result = $this->repository->toggleTester($userId);
        $user = $result['user'];
        $isTester = $result['is_tester'];

        $statusText = $isTester ? 'dijadikan Akun Penguji (Tester)' : 'dihapus dari Akun Penguji (Tester)';

        AuditLogger::userAction(
            'tester_status_toggled',
            "Pengguna {$user->nama} (@{$user->username}) {$statusText}",
            ['user_id' => $user->id, 'is_tester' => $isTester]
        );

        return [
            'success' => true,
            'is_tester' => $isTester,
            'message' => "Pengguna {$user->nama} berhasil {$statusText}."
        ];
    }
}
