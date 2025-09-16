<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function guruOlahraga(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isGuruOlahraga()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        // Statistik umum
        $totalSiswa = User::where('level', 'Siswa')->count();
        $totalKelas = Kelas::count();
        
        // Statistik IMT
        $statistikIMT = Kesehatan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Data per kelas
        $dataKelas = Kelas::with(['users' => function($query) {
            $query->where('level', 'Siswa');
        }])->get()->map(function($kelas) {
            $siswaIds = $kelas->users->pluck('id');
            
            $kesehatanTerbaru = Kesehatan::whereIn('id_user', $siswaIds)
                ->select('id_user', 'status')
                ->whereIn('id_kesehatan', function($query) use ($siswaIds) {
                    $query->select(DB::raw('MAX(id_kesehatan)'))
                        ->from('kesehatan')
                        ->whereIn('id_user', $siswaIds)
                        ->groupBy('id_user');
                })
                ->get()
                ->groupBy('status');

            return [
                'id' => $kelas->id,
                'kelas' => $kelas->kelas,
                'jurusan' => $kelas->jurusan,
                'total_siswa' => $kelas->users->count(),
                'statistik' => [
                    'normal' => $kesehatanTerbaru->get('Normal', collect())->count(),
                    'kurus' => $kesehatanTerbaru->get('Kurus', collect())->count(),
                    'overweight' => $kesehatanTerbaru->get('Overweight', collect())->count(),
                    'obesitas' => $kesehatanTerbaru->get('Obesitas', collect())->count(),
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'statistik_umum' => [
                    'total_siswa' => $totalSiswa,
                    'total_kelas' => $totalKelas,
                    'statistik_imt' => $statistikIMT,
                ],
                'data_kelas' => $dataKelas
            ]
        ]);
    }

    public function siswaDetail(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->isSiswa() && $user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $siswa = User::with('kelas')->find($id);
        
        if (!$siswa || $siswa->level !== 'Siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.'
            ], 404);
        }

        // Data kesehatan terbaru
        $kesehatanTerbaru = Kesehatan::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->first();

        // Data HB terbaru
        $hbTerbaru = Hb::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->first();

        // Riwayat kesehatan (5 terakhir)
        $riwayatKesehatan = Kesehatan::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->take(5)
            ->get();

        // Riwayat HB (5 terakhir)
        $riwayatHb = Hb::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => $siswa,
                'kesehatan_terbaru' => $kesehatanTerbaru,
                'hb_terbaru' => $hbTerbaru,
                'riwayat_kesehatan' => $riwayatKesehatan,
                'riwayat_hb' => $riwayatHb,
            ]
        ]);
    }
}