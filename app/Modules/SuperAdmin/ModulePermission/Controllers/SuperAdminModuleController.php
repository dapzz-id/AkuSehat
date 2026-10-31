<?php

namespace App\Modules\SuperAdmin\ModulePermission\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\ModulePermission\Services\ModulePermissionService;
use App\Modules\SuperAdmin\ModulePermission\Requests\UpdateGlobalModuleRequest;
use App\Modules\SuperAdmin\ModulePermission\Requests\FilterTesterRequest;
use Illuminate\Http\JsonResponse;

class SuperAdminModuleController extends Controller
{
    public function __construct(
        protected ModulePermissionService $modulePermissionService
    ) {}

    public function index(FilterTesterRequest $request)
    {
        $testerSearch = $request->validated('tester_search');
        $data = $this->modulePermissionService->getSuperAdminDashboardData($testerSearch);

        return view('superadmin.modules.index', $data);
    }

    public function update(UpdateGlobalModuleRequest $request)
    {
        $this->modulePermissionService->updateGlobalModules($request->validated()['modules']);

        return redirect()->route('superadmin.modules.index')
            ->with('success', 'Konfigurasi akses fitur dan modul global berhasil diperbarui.');
    }

    public function toggleTester(int|string $userId): JsonResponse
    {
        $result = $this->modulePermissionService->toggleTesterStatus($userId);

        return response()->json($result);
    }
}
