<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\PeminjamanPita;
use App\Models\Kelas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdminPMR()) {
            $data = [
                'total_siswa' => User::where('level', 'Siswa')->count(),
                'total_kelas' => Kelas::count(),
                'data_kesehatan_bulan_ini' => Kesehatan::whereMonth('created_at', now()->month)->count(),
                'data_hb_bulan_ini' => Hb::whereMonth('created_at', now()->month)->count(),
            ];

            $dataByClass = Kelas::withCount([
                'users as siswa' => function($query) {
                    $query->where('level', 'Siswa');
                },
            ])->with('kesehatan')->get();
            return view('dashboard.admin-pmr', compact('data', 'dataByClass'));
        }
        
        if ($user->isGuruBK()) {
            $data = [
                'total_siswi' => User::where('level', 'Siswa')->where('jk', 'P')->count(),
                'pinjaman_aktif' => PeminjamanPita::where('status', 'dipinjam')->count(),
                'pinjaman_terlambat' => PeminjamanPita::where('status', 'terlambat')->count(),
                'warning_count' => PeminjamanPita::where('status', 'dipinjam')
                    ->where('estimasi_selesai_haid', '<', now())
                    ->count(),
            ];
            return view('dashboard.guru-bk', compact('data'));
        }
        
        return redirect('/login');
    }
}