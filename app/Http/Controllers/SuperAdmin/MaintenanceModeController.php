<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceMode;
use Illuminate\Support\Facades\Artisan;

class MaintenanceModeController extends Controller
{
    public function index()
    {
        $maintenances = MaintenanceMode::all();
        return view('superadmin.maintenance.index', compact('maintenances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:web,mobile,both',
        ]);

        $mode = MaintenanceMode::firstOrCreate([], [
            'is_web_maintenance' => 0,
            'is_mobile_maintenance' => 0,
        ]);

        // Cek apakah sedang aktif
        if ($mode->is_web_maintenance && in_array($request->platform, ['web', 'both'])) {
            return redirect()->route('superadmin.maintenance.index')
                ->with('error', 'Maintenance mode for Web is already active.');
        }

        if ($mode->is_mobile_maintenance && in_array($request->platform, ['mobile', 'both'])) {
            return redirect()->route('superadmin.maintenance.index')
                ->with('error', 'Maintenance mode for Mobile is already active.');
        }

        // Aktifkan sesuai platform
        if (in_array($request->platform, ['web', 'both'])) {
            $mode->update(['is_web_maintenance' => 1]);
        }

        if (in_array($request->platform, ['mobile', 'both'])) {
            $mode->update(['is_mobile_maintenance' => 1]);
        }

        return redirect()->route('superadmin.maintenance.index')
            ->with('success', 'Maintenance mode activated successfully.');
    }

    public function update(Request $request, $id)
    {
        $mode = MaintenanceMode::findOrFail($id);

        $request->validate([
            'platform' => 'required|in:web,mobile,both',
        ]);

        // Cek apakah sedang tidak aktif
        if (!$mode->is_web_maintenance && in_array($request->platform, ['web', 'both'])) {
            return redirect()->route('superadmin.maintenance.index')
                ->with('error', 'Maintenance mode for Web is not active.');
        }

        if (!$mode->is_mobile_maintenance && in_array($request->platform, ['mobile', 'both'])) {
            return redirect()->route('superadmin.maintenance.index')
                ->with('error', 'Maintenance mode for Mobile is not active.');
        }

        // Nonaktifkan sesuai platform
        if (in_array($request->platform, ['web', 'both'])) {
            $mode->update(['is_web_maintenance' => 0]);
        }

        if (in_array($request->platform, ['mobile', 'both'])) {
            $mode->update(['is_mobile_maintenance' => 0]);
        }

        return redirect()->route('superadmin.maintenance.index')
            ->with('success', 'Maintenance mode deactivated successfully.');
    }

    public function toggleUpdate($id, Request $request)
    {
        $request->validate([
            'platform' => 'required|in:web,mobile,both',
        ]);

        $mode = MaintenanceMode::findOrFail($id);

        $secret = env('MAINTENANCE_SECRET', 'superadmin-bypass-123');

        // Toggle sesuai platform
        if (in_array($request->platform, ['web', 'both'])) {
            $newState = !$mode->is_web_maintenance;
            $mode->update(['is_web_maintenance' => $newState]);

            if ($newState) {
                // Aktifkan maintenance
                Artisan::call('down');
            } else {
                // Nonaktifkan maintenance
                Artisan::call('up');
            }
        }

        if (in_array($request->platform, ['mobile', 'both'])) {
            $mode->update(['is_mobile_maintenance' => !$mode->is_mobile_maintenance]);
        }

        return redirect()->route('superadmin.maintenance.index')
            ->with('success', 'Mode pemeliharaan berhasil diubah.');
    }
}
