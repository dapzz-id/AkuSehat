<?php

namespace App\Modules\SuperAdmin\ModulePermission\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\SuperAdmin\ModulePermission\Services\ModulePermissionService;
use App\Modules\SuperAdmin\ModulePermission\Requests\UpdateInstansiModuleRequest;

class ModulePermissionController extends Controller
{
    public function __construct(
        protected ModulePermissionService $modulePermissionService
    ) {}

    public function index()
    {
        $data = $this->modulePermissionService->getInstansiModulesData(Auth::user());

        if (!$data) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung ke instansi mana pun.');
        }

        return view('admin.modules.index', $data);
    }

    public function update(UpdateInstansiModuleRequest $request)
    {
        $user = Auth::user();
        $instansi = $user->instansi;

        if (!$instansi) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung ke instansi mana pun.');
        }

        // Accept active_modules from the checkboxes form, falling back to modules
        $activeModules = $request->input('active_modules', $request->input('modules', []));
        if (!is_array($activeModules)) {
            $activeModules = [];
        }

        $roleLabels = $request->input('role_labels', []);
        $permissions = $request->input('permissions', []);

        $this->modulePermissionService->updateInstansiConfig($instansi, $activeModules, $roleLabels, $permissions);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Konfigurasi modul dan hak akses institusi Anda berhasil disimpan.');
    }
}
