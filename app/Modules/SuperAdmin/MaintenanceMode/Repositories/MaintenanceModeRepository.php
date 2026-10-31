<?php

namespace App\Modules\SuperAdmin\MaintenanceMode\Repositories;

use App\Models\MaintenanceMode;

class MaintenanceModeRepository
{
    public function getOrCreate(): MaintenanceMode
    {
        return MaintenanceMode::firstOrCreate([], [
            'is_web_maintenance' => false,
            'is_mobile_maintenance' => false,
        ]);
    }

    public function update(MaintenanceMode $mode, array $data): bool
    {
        return $mode->update($data);
    }
}
