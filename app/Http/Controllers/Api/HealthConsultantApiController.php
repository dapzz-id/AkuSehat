<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kesehatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HealthConsultantApiController extends Controller
{
    public function healthSummary(Request $req) {
        // hitung status berdasarkan BMI dari pemeriksaan terakhir tiap member
        $latest = DB::table('kesehatan as k')
            ->select('k.*')
            ->join(DB::raw('(select id_user, max(tgl) as max_tgl from kesehatan group by id_user) as lastk'),
                function($join) { $join->on('k.id_user', '=', 'lastk.id_user')->on('k.tgl','=','lastk.max_tgl'); })
            ->get();

        $total=0; $ob=0; $kr=0; $nm=0;
        foreach ($latest as $row) {
            $total++;
            $bb = floatval($row->bb ?? 0);
            $tb = floatval($row->tb ?? 0)/100.0;
            $bmi = $tb>0 ? $bb/($tb*$tb):null;
            if ($bmi===null) continue;
            if ($bmi >= 27) $ob++;
            else if ($bmi < 18.5) $kr++;
            else $nm++;
        }
        return response()->json(['status'=>true,'data'=>[
            'total'=>$total,'obesitas'=>$ob,'kurus'=>$kr,'normal'=>$nm
        ]]);
    }

    public function healthByClass(Request $req)
    {
        $rows = DB::table('users as u')
            ->leftJoin('kelas as c', 'c.id', '=', 'u.id_kelas')
            ->select('u.id', 'u.id_kelas', 'c.kelas', 'c.jurusan', 'u.nama', 'u.nis', 'u.jk', 'u.username')
            ->where('u.level', 'Member')
            ->get();

        $result = [];
        $currentYear = date('Y');
        $startYear = intval($currentYear) - 3;

        // Label bulan (1-12)
        $monthLabels = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        foreach ($rows as $u) {
            $key = $u->id_kelas ?: 0;

            if (!isset($result[$key])) {
                $result[$key] = [
                    'id' => $u->id_kelas ?: 0,
                    'kelas' => $u->kelas ?: 'Belum ditentukan',
                    'jurusan' => $u->jurusan ?: '-',
                    'obesitas' => 0,
                    'kurus' => 0,
                    'normal' => 0,
                    'total_member' => \App\Models\User::where('level', 'Member')
                        ->where('id_kelas', $u->id_kelas)
                        ->where('sekolah_id', Auth::user()->sekolah_id)
                        ->count(),
                    'data_member' => []
                ];
            }

            // Ambil semua data kesehatan member
            $kesehatanList = Kesehatan::where('id_user', $u->id)
                ->orderBy('tgl', 'asc')
                ->get();

            // Hitung status BMI terakhir untuk akumulasi per kelas
            $last = $kesehatanList->last();
            if ($last) {
                $bb = floatval($last->bb ?? 0);
                $tb = floatval($last->tb ?? 0) / 100.0;
                $bmi = $tb > 0 ? $bb / ($tb * $tb) : null;

                if ($bmi !== null) {
                    if ($bmi >= 27) $result[$key]['obesitas']++;
                    else if ($bmi < 18.5) $result[$key]['kurus']++;
                    else $result[$key]['normal']++;
                }
            }

            // Riwayat kesehatan langsung dari data yang ada
            $riwayat = [];
            $totalBmi = 0;
            $count = 0;

            foreach ($kesehatanList as $k) {
                $bb = floatval($k->bb ?? 0);
                $tb = floatval($k->tb ?? 0) / 100.0;
                $bmi = $tb > 0 ? round($bb / ($tb * $tb), 1) : 0;

                if ($bmi > 0) {
                    $totalBmi += $bmi;
                    $count++;
                }

                $riwayat[] = [
                    'tgl' => $k->tgl,
                    'bb' => $k->bb,
                    'tb' => $k->tb,
                    'score' => $bmi,
                    'status' => $k->status ?? null,
                    'status_darah' => $k->status_darah ?? null,
                    'perilaku_beresiko' => $k->perilaku_beresiko ?? null,
                    'gangguan_reproduksi' => $k->gangguan_reproduksi ?? null,
                ];
            }

            // Rata-rata BMI dari semua data yang ada
            $avgBmi = $count > 0 ? round($totalBmi / $count, 1) : 0;

            // Masukkan ke data member
            $result[$key]['data_member'][] = [
                'id' => $u->id,
                'nama' => $u->nama,
                'nis' => $u->nis,
                'jk' => $u->jk,
                'username' => $u->username,
                'pemeriksaan_terakhir' => $last?->updated_at,
                'avg_bmi' => $avgBmi,
                'riwayat_kesehatan' => $riwayat
            ];
        }

        return response()->json([
            'status' => true,
            'data' => array_values($result)
        ]);
    }

    public function healthNotification(Request $req) {
        $kesehatan = Kesehatan::with('user:id,nama,level')
        ->whereHas('user', function ($q) {
            $q->where('level', 'Member');
        })
        ->where(function ($query) {
            $query
                ->where(function ($q) {
                    $q->whereRaw('LOWER(COALESCE(status, "")) != ?', ['normal'])
                    ->whereRaw('TRIM(COALESCE(status, "")) != ""');
                })
                ->orWhere(function ($q) {
                    $q->whereRaw('LOWER(COALESCE(status_darah, "")) != ?', ['normal'])
                    ->whereRaw('TRIM(COALESCE(status_darah, "")) != ""');
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('perilaku_beresiko')
                    ->whereRaw('TRIM(COALESCE(perilaku_beresiko, "")) != ""')
                    ->whereRaw('LOWER(TRIM(perilaku_beresiko)) != ?', ['sehat']);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('gangguan_reproduksi')
                    ->whereRaw('TRIM(COALESCE(gangguan_reproduksi, "")) != ""')
                    ->whereRaw('LOWER(TRIM(gangguan_reproduksi)) != ?', ['sehat']);
                });
        })
        ->orderBy('tgl', 'desc')
        ->get();

        return response()->json(['status'=>true,'data'=>$kesehatan]);
    }
}
