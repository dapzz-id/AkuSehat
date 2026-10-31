<?php

namespace App\Modules\User\ManageUser\Services;

use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Modules\SuperAdmin\LicenseKey\Enums\LicenseStatus;
use App\Models\SoftwareApp;
use App\Models\MaintenanceMode;
use App\Models\Views\InstansiHealthSummary;
use App\Models\Views\DivisiDashboardStat;
use App\Models\UserActivity;
use App\Models\UserVitalLog;

class DashboardService
{
    public function getSuperAdminDashboardData(): array
    {
        $totalSekolah = Instansi::count();

        $licenseAktif = LicenseKey::whereIn('status', [LicenseStatus::ACTIVE->value, 'aktif'])
            ->where('tanggal_berakhir', '>=', now())
            ->count();

        $licenseExpired = LicenseKey::whereIn('status', [LicenseStatus::EXPIRED->value, 'expired'])->count();
        $totalUsers = User::count();

        $licenseAkanExpired = LicenseKey::whereIn('status', [LicenseStatus::ACTIVE->value, 'aktif'])
            ->whereBetween('tanggal_berakhir', [now(), now()->addDays(30)])
            ->with('instansi')
            ->get();

        $sekolahTerbaru = Instansi::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $latestUpdate = SoftwareApp::orderBy('release_date', 'desc')->first();

        $maintenanceWeb = MaintenanceMode::isWebInMaintenance();
        $maintenanceMobile = MaintenanceMode::isMobileInMaintenance();

        return compact(
            'totalSekolah',
            'licenseAktif',
            'licenseExpired',
            'totalUsers',
            'licenseAkanExpired',
            'sekolahTerbaru',
            'latestUpdate',
            'maintenanceWeb',
            'maintenanceMobile'
        );
    }

    public function getAdminDashboardData(User $user): array
    {
        $summary = InstansiHealthSummary::where('instansi_id', $user->instansi_id)->first();

        $data = [
            'total_member' => $summary ? $summary->total_member : 0,
            'total_divisi' => $summary ? $summary->total_divisi : 0,
            'data_kesehatan_tahun_ini' => $summary ? $summary->total_kesehatan_tahun_ini : 0,
            'data_hb_tahun_ini' => $summary ? $summary->total_hb_tahun_ini : 0,
        ];

        $dataByClass = DivisiDashboardStat::where('instansi_id', $user->instansi_id)
            ->orderBy('divisi_name', 'asc')
            ->get();

        return compact('data', 'dataByClass');
    }

    public function getMemberDashboardData(User $user): array
    {
        $instansi = $user->instansi;
        $kesehatan = Kesehatan::where('id_user', $user->id)->latest()->first();
        $hb = Hb::where('id_user', $user->id)->latest()->first();
        $activities = UserActivity::where('user_id', $user->id)->latest()->limit(5)->get();
        $vitalLogs = UserVitalLog::where('user_id', $user->id)->latest()->limit(7)->get();

        return compact('user', 'instansi', 'kesehatan', 'hb', 'activities', 'vitalLogs');
    }
}
