<?php

namespace App\Modules\SuperAdmin\MaintenanceMode\Services;

use App\Models\MaintenanceMode;
use App\Modules\SuperAdmin\MaintenanceMode\Repositories\MaintenanceModeRepository;
use App\Modules\SuperAdmin\MaintenanceMode\Enums\MaintenancePlatform;
use App\Services\AuditLogger;

class MaintenanceModeService
{
    public function __construct(
        protected MaintenanceModeRepository $repository
    ) {}

    public function getMaintenanceMode(): MaintenanceMode
    {
        return $this->repository->getOrCreate();
    }

    public function togglePlatform(string $platformInput): array
    {
        $mode = $this->repository->getOrCreate();
        $platform = MaintenancePlatform::tryFrom($platformInput) ?? MaintenancePlatform::BOTH;

        if ($platform === MaintenancePlatform::WEB) {
            $mode->is_web_maintenance = !$mode->is_web_maintenance;
            $platformName = $platform->label();
            $statusText = $mode->is_web_maintenance ? 'diaktifkan' : 'dinonaktifkan';
        } elseif ($platform === MaintenancePlatform::MOBILE) {
            $mode->is_mobile_maintenance = !$mode->is_mobile_maintenance;
            $platformName = $platform->label();
            $statusText = $mode->is_mobile_maintenance ? 'diaktifkan' : 'dinonaktifkan';
        } else {
            $mode->is_web_maintenance = !$mode->is_web_maintenance;
            $mode->is_mobile_maintenance = !$mode->is_mobile_maintenance;
            $platformName = MaintenancePlatform::BOTH->label();
            $statusText = 'diperbarui';
        }

        $mode->save();

        AuditLogger::maintenance(
            'maintenance_mode_toggled',
            "Mode pemeliharaan sistem ({$platformName}) {$statusText} oleh Super Administrator.",
            [
                'platform' => $platform->value,
                'is_web' => $mode->is_web_maintenance,
                'is_mobile' => $mode->is_mobile_maintenance,
            ]
        );

        return [
            'mode' => $mode,
            'message' => "Mode pemeliharaan ({$platformName}) berhasil {$statusText}."
        ];
    }
}
