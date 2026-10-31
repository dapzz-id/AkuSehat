<?php

namespace App\Modules\SuperAdmin\MaintenanceMode\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\MaintenanceMode\Services\MaintenanceModeService;
use App\Modules\SuperAdmin\MaintenanceMode\Requests\ToggleMaintenanceRequest;

class MaintenanceModeController extends Controller
{
    public function __construct(
        protected MaintenanceModeService $maintenanceModeService
    ) {}

    public function index()
    {
        $mode = $this->maintenanceModeService->getMaintenanceMode();
        return view('superadmin.maintenance.index', compact('mode'));
    }

    public function toggleUpdate(ToggleMaintenanceRequest $request, $id = null)
    {
        $result = $this->maintenanceModeService->togglePlatform($request->validated()['platform']);

        return redirect()->route('superadmin.maintenance.index')
            ->with('success', $result['message']);
    }
}
