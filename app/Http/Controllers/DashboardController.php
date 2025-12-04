<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\PeminjamanPita;
use App\Models\Divisi;
use App\Models\Instansi;
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
            $totalInstansi = Instansi::count();

            $licenseAktif = LicenseKey::where('status', 'aktif')
                ->where('tanggal_berakhir', '>=', now())
                ->count();

            $licenseExpired = LicenseKey::where('status', 'expired')->count();
            $totalUsers = User::count();

            // License yang akan expired dalam 30 hari
            $licenseAkanExpired = LicenseKey::where('status', 'active')
                ->whereBetween('tanggal_berakhir', [now(), now()->addDays(30)])
                ->with('instansi')
                ->get();

            // Instansi terbaru
            $instansiTerbaru = Instansi::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Latest update
            $latestUpdate = SoftwareApp::orderBy('release_date', 'desc')->first();

            // Maintenance status
            $maintenanceWeb = MaintenanceMode::isWebInMaintenance();
            $maintenanceMobile = MaintenanceMode::isMobileInMaintenance();
            
            return view('dashboard.superadmin', compact(
                'totalInstansi',
                'licenseAktif',
                'licenseExpired',
                'totalUsers',
                'licenseAkanExpired',
                'instansiTerbaru',
                'latestUpdate',
                'maintenanceWeb',
                'maintenanceMobile'
            ));
        }

        if(!app()->isDownForMaintenance()){
            if ($user->isAdmin()) {
                $data = [
                    'total_member' => User::where('level', '!=', 'Admin Instansi')
                                    ->whereNotNull('instansi_id')
                                    ->where('instansi_id', Auth::user()->instansi_id)->count(),

                    'total_divisi' => Divisi::where('instansi_id', Auth::user()->instansi_id)->count(),

                    'data_kesehatan_tahun_ini' => Kesehatan::whereHas('user', function ($q) {
                                                    $q->where('level', '!=', 'Admin Instansi')
                                                    ->where('instansi_id', Auth::user()->instansi_id);
                                                })
                                                ->whereYear('tgl', now()->year)->count(),
                    'data_hb_tahun_ini' => Hb::whereHas('user', function ($q) {
                                                    $q->where('level', '!=', 'Admin Instansi')
                                                    ->where('instansi_id', Auth::user()->instansi_id);
                                                })
                                                ->whereYear('tgl', now()->year)->count(),
                ];

                $dataByClass = Divisi::withCount([
                    'users as total_member' => function ($query) {
                        $query->where('level', '=', 'Member')
                        ->whereNotNull('instansi_id')
                        ->where('instansi_id', Auth::user()->instansi_id);
                    },
                ])
                ->whereNotNull('instansi_id')
                ->where('instansi_id', Auth::user()->instansi_id)
                ->with(['users' => function ($q) {
                    $q->where('level', '!=', 'Admin Instansi')
                    ->whereNotNull('instansi_id')
                    ->where('instansi_id', Auth::user()->instansi_id)->with('kesehatan');
                }])
                ->orderBy('divisi_name', 'asc')
                ->get();

                return view('dashboard.admin', compact('data', 'dataByClass'));
            }

            return redirect('/login');
        }else{
            throw new HttpException(503, 'Website sedang dalam pemeliharaan.');
        }
    }
}