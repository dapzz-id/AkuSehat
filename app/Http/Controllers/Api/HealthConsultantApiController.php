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
    public function healthSummary(Request $req)
    {
        $latest = Kesehatan::query()
            ->whereHas('user', function($q) {
                $q->where('level', 'Member')
                ->where('instansi_id', Auth::user()->instansi_id);
            })
            ->whereYear('tgl', now()->year)
            ->join(DB::raw('(select id_user, max(tgl) as max_tgl 
                            from kesehatan 
                            group by id_user) as lastk'),
                function($join) {
                    $join->on('kesehatan.id_user', '=', 'lastk.id_user')
                        ->on('kesehatan.tgl', '=', 'lastk.max_tgl');
                }
            )
            ->select('kesehatan.*')
            ->get();

        $total = 0; $ob = 0; $kr = 0; $nm = 0;

        foreach ($latest as $row) {
            $total++;
            $bb = floatval($row->bb ?? 0);
            $tb = floatval($row->tb ?? 0) / 100;
            $bmi = $tb > 0 ? $bb / ($tb * $tb) : null;

            if ($bmi === null) continue;

            if ($bmi >= 27) $ob++;
            else if ($bmi < 18.5) $kr++;
            else $nm++;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total' => $total,
                'obesitas' => $ob,
                'kurus' => $kr,
                'normal' => $nm,
            ]
        ]);
    }

    public function healthByDivision(Request $req)
    {
        $rows = DB::table('users as u')
            ->leftJoin('divisi as c', 'c.id', '=', 'u.id_divisi')
            ->select('u.id', 'u.id_divisi', 'c.divisi_name', 'u.nama', 'u.nomor_induk', 'u.jk', 'u.username')
            ->where('u.instansi_id', Auth::user()->instansi_id)
            ->where('u.level', 'Member')
            ->orderBy('c.divisi_name', 'asc')
            ->orderBy('u.nama', 'asc')
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
            $key = $u->id_divisi ?: 0;

            if (!isset($result[$key])) {
                $result[$key] = [
                    'id' => $u->id_divisi ?: 0,
                    'divisi' => $u->divisi_name ?: 'Belum ditentukan',
                    'obesitas' => 0,
                    'kurus' => 0,
                    'normal' => 0,
                    'total_member' => \App\Models\User::where('level', 'Member')
                        ->where('id_divisi', $u->id_divisi)
                        ->where('instansi_id', Auth::user()->instansi_id)
                        ->count(),
                    'data_member' => []
                ];
            }

            // Ambil semua data kesehatan member
            $kesehatanList = Kesehatan::where('id_user', $u->id)
                ->whereHas('user', function ($q) {
                    $q->where('instansi_id', Auth::user()->instansi_id);
                })
                ->whereYear('tgl', now()->year)
                ->orderBy('tgl', 'desc')
                ->get();

            // Hitung status BMI terakhir untuk akumulasi per divisi
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
                'nomor_induk' => $u->nomor_induk,
                'divisi' => $u->divisi_name ?: 'Belum ditentukan',
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
            $q->where('instansi_id', Auth::user()->instansi_id);
        })
        ->whereYear('tgl', now()->year)
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
                    ->whereNotIn(DB::raw('LOWER(TRIM(perilaku_beresiko))'), ['sehat', 'normal', 'tidak ada']);
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('gangguan_reproduksi')
                    ->whereRaw('TRIM(COALESCE(gangguan_reproduksi, "")) != ""')
                    ->whereNotIn(DB::raw('LOWER(TRIM(gangguan_reproduksi))'), ['sehat', 'normal', 'tidak ada']);
                });
        })
        ->orderBy('tgl', 'desc')
        ->get();

        return response()->json(['status'=>true,'data'=>$kesehatan]);
    }
}
