<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\PeminjamanPita;
use App\Models\Kelas;
use App\Models\Sekolah;
use App\Models\LicenseKey;
use App\Models\SoftwareApp;
use App\Models\MaintenanceMode;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin()) {
            $totalSekolah = Sekolah::count();

            $licenseAktif = LicenseKey::where('status', 'aktif')
                ->where('tanggal_berakhir', '>=', now())
                ->count();

            $licenseExpired = LicenseKey::where('status', 'expired')->count();
            $totalUsers = User::count();

            // License yang akan expired dalam 30 hari
            $licenseAkanExpired = LicenseKey::where('status', 'active')
                ->whereBetween('tanggal_berakhir', [now(), now()->addDays(30)])
                ->with('sekolah')
                ->get();

            // Sekolah terbaru
            $sekolahTerbaru = Sekolah::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Latest update
            $latestUpdate = SoftwareApp::orderBy('release_date', 'desc')->first();

            // Maintenance status
            $maintenanceWeb = MaintenanceMode::isWebInMaintenance();
            $maintenanceMobile = MaintenanceMode::isMobileInMaintenance();
            
            return view('dashboard.superadmin', compact(
                'totalSekolah',
                'licenseAktif',
                'licenseExpired',
                'totalUsers',
                'licenseAkanExpired',
                'sekolahTerbaru',
                'latestUpdate',
                'maintenanceWeb',
                'maintenanceMobile'
            ));
        }

        if(!app()->isDownForMaintenance()){
            if ($user->isAdmin()) {
                $data = [
                    'total_member' => User::where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)->count(),
                    'total_kelas' => Kelas::where('sekolah_id', Auth::user()->sekolah_id)->count(),
                    'data_kesehatan_bulan_ini' => Kesehatan::whereMonth('created_at', now()->month)->count(),
                    'data_hb_bulan_ini' => Hb::whereMonth('created_at', now()->month)->count(),
                ];

                $dataByClass = Kelas::withCount([
                    'users as total_member' => function ($query) {
                        $query->where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id);
                    },
                ])
                ->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)
                ->with(['users' => function ($q) {
                    $q->where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)->with('kesehatan');
                }])
                ->get();

                return view('dashboard.admin', compact('data', 'dataByClass'));
            }
            
            if ($user->isHealthMonitor()) {
                $data = [
                    'total_siswi' => User::where('level', 'Member')->where('jk', 'P')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)->count(),
                    'pinjaman_aktif' => PeminjamanPita::with('user')->where('status', 'dipinjam')->whereHas('user', function ($query) {
                        $query->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id);
                    })->count(),
                    'warning_count' => PeminjamanPita::with('user')->whereIn('status', ['dipinjam', 'terlambat'])->whereHas('user', function ($query) {
                        $query->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id);
                    })->where('estimasi_selesai_haid', '<', now())->count(),
                ];
                return view('dashboard.guru-bk', compact('data'));
            }

            return redirect('/login');
        }else{
            throw new HttpException(503, 'Website sedang dalam pemeliharaan.');
        }
    }
}