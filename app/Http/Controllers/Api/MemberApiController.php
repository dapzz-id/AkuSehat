<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataHaid;
use App\Models\PeminjamanPita;
use Carbon\Carbon;

class MemberApiController extends Controller
{
    public function me(Request $req) {
        $u = $req->user();
        $divisi = DB::table('divisi')->where('id', $u->id_divisi)->first();
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $u->id,
                'nama' => $u->nama,
                'username' => $u->username,
                'nomor_induk' => $u->nomor_induk,
                'divisi' => $divisi->divisi_name ?? null,
                'jk' => $u->jk,
                'level' => $u->level,
            ]
        ]);
    }

    public function changePassword(Request $req) {
        $u = $req->user();
        $data = $req->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ]);

        if (!password_verify($data['current_password'], $u->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama tidak sesuai.'
            ], 400);
        }

        if($data['current_password'] === $data['new_password']) {
            return response()->json([
                'status' => false,
                'message' => 'Password baru tidak boleh sama dengan password lama.'
            ], 400);
        }

        if ($data['new_password'] !== $data['new_password_confirmation']) {
            return response()->json([
                'status' => false,
                'message' => 'Konfirmasi password baru tidak sesuai.'
            ], 400);
        }

        $u->password = bcrypt($data['new_password']);
        $u->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }

    public function notifications(Request $req) {
        return response()->json([
            'status' => true,
            'data' => [
                ['id'=>1,'title'=>'Pemeriksaan berkala','message'=>'Jangan lupa cek kesehatan besok.','created_at'=>now()->toDateTimeString()],
                ['id'=>2,'title'=>'Pengajuan diterima','message'=>'Peminjaman pita disetujui.','created_at'=>now()->toDateTimeString()],
            ]
        ]);
    }

    public function summary(Request $req) {
        $u = $req->user();
        $last = DB::table('kesehatan')->where('id_user', $u->id)->orderByDesc('tgl')->first();
        $bmi = null; $status = null;
        if ($last) {
            // asumsikan bb=kg, tb=cm
            $bb = floatval($last->bb ?? 0);
            $tb = floatval($last->tb ?? 0) / 100.0;
            $bmi = $tb > 0 ? $bb / ($tb * $tb) : null;
        }
        return response()->json([
            'status' => true,
            'data' => [
                'bmi' => $bmi,
                'sistol' => $last->sistol ?? null,
                'diastol' => $last->diastol ?? null,
                'last_check' => $last->tgl ?? null,
                'tinggi' => $last->tb ?? null,
                'berat' => $last->bb ?? null,
                'hb' => DB::table('hb')->where('id_user', $u->id)->orderByDesc('tgl')->value('hb') ?? null,
            ]
        ]);
    }

    public function summaryGrafik(Request $req)
    {
        $u = $req->user();

        // Ambil data kesehatan 5 tahun terakhir
        $records = DB::table('kesehatan')
            ->select(
                DB::raw("YEAR(tgl) as year"),
                DB::raw("MONTH(tgl) as month"),
                DB::raw("
                    COALESCE(AVG(CASE
                        WHEN LOWER(status_darah) = 'normal'
                            AND LOWER(status) = 'normal'
                            AND (LOWER(kondisi_telinga) = 'sehat' OR LOWER(kondisi_telinga) = 'normal' OR LOWER(kondisi_telinga) = 'tidak ada' OR kondisi_telinga IS NULL OR kondisi_telinga = '')
                            AND (LOWER(kondisi_gigi) = 'sehat' OR LOWER(kondisi_gigi) = 'normal' OR LOWER(kondisi_gigi) = 'tidak ada' OR kondisi_gigi IS NULL OR kondisi_gigi = '')
                            AND (perilaku_beresiko IS NULL OR LOWER(perilaku_beresiko) = 'tidak ada' OR LOWER(perilaku_beresiko) = '' OR LOWER(perilaku_beresiko) = 'normal' OR LOWER(perilaku_beresiko) = 'sehat')
                            AND (gangguan_reproduksi IS NULL OR LOWER(gangguan_reproduksi) = 'tidak ada' OR LOWER(gangguan_reproduksi) = '' OR LOWER(gangguan_reproduksi) = 'normal' OR LOWER(gangguan_reproduksi) = 'sehat')
                        THEN 100

                        WHEN LOWER(status) IN ('underweight', 'kurus', 'overweight', 'obesitas', 'obesitas level 1', 'obesitas level 2', 'obesitas level 3')
                            OR LOWER(status_darah) IN ('hipotensi', 'rendah', 'elevasi', 'prahipertensi', 'hipertensi tahap 1', 'tinggi', 'hipertensi tahap 2', 'krisis')
                        THEN 
                            (
                                CASE 
                                    WHEN LOWER(status) = 'underweight' OR LOWER(status) = 'kurus' THEN 60
                                    WHEN LOWER(status) = 'normal' THEN 100
                                    WHEN LOWER(status) = 'overweight' THEN 70
                                    WHEN LOWER(status) = 'obesitas' OR LOWER(status) = 'obesitas level 1' THEN 40
                                    WHEN LOWER(status) = 'obesitas level 2' THEN 25
                                    WHEN LOWER(status) = 'obesitas level 3' THEN 15
                                    ELSE 50
                                END +
                                CASE 
                                    WHEN LOWER(status_darah) = 'hipotensi' OR LOWER(status_darah) = 'rendah' THEN 60
                                    WHEN LOWER(status_darah) = 'normal' THEN 100
                                    WHEN LOWER(status_darah) = 'elevasi' OR LOWER(status_darah) = 'prahipertensi' THEN 80
                                    WHEN LOWER(status_darah) = 'hipertensi tahap 1' OR LOWER(status_darah) = 'tinggi' THEN 65
                                    WHEN LOWER(status_darah) = 'hipertensi tahap 2' THEN 40
                                    WHEN LOWER(status_darah) = 'krisis' THEN 20
                                    ELSE 50
                                END
                            ) / 2

                        WHEN (LOWER(kondisi_telinga) NOT IN ('sehat', 'normal', 'tidak ada') AND kondisi_telinga IS NOT NULL AND kondisi_telinga != '')
                            OR (LOWER(kondisi_gigi) NOT IN ('sehat', 'normal', 'tidak ada') AND kondisi_gigi IS NOT NULL AND kondisi_gigi != '')
                        THEN 60

                        WHEN (LOWER(perilaku_beresiko) NOT IN ('sehat', 'normal', 'tidak ada') AND perilaku_beresiko IS NOT NULL AND perilaku_beresiko != '')
                            OR (LOWER(gangguan_reproduksi) NOT IN ('sehat', 'normal', 'tidak ada') AND gangguan_reproduksi IS NOT NULL AND gangguan_reproduksi != '')
                        THEN 35

                        ELSE 50
                    END), 50) as score
                ")
            )
            ->where('id_user', $u->id)
            ->where('tgl', '>=', now()->subYears(5)->startOfYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $months = collect();
        $start = now()->subYears(5)->startOfYear();
        $end = now()->endOfYear();

        while ($start <= $end) {
            $months->push([
                'year' => (int) $start->year,
                'month' => (int) $start->month,
                'label' => $start->format('M Y'),
                'score' => 0,
            ]);
            $start->addMonth();
        }

        // Gabungkan hasil query ke template bulan
        $filled = $months->map(function ($m) use ($records) {
            $found = $records->first(fn($r) => $r->year == $m['year'] && $r->month == $m['month']);
            if ($found) $m['score'] = round(min($found->score, 100), 2);
            return $m;
        });

        // Hitung rata-rata per tahun berdasarkan data bulan yang ada
        $byYear = $filled->groupBy('year')->map(function ($months) {
            $validMonths = $months->filter(fn($m) => $m['score'] > 0);
            $avg = $validMonths->avg('score') ?? 0;
            return [
                'year' => $months->first()['year'],
                'avg_score' => round($avg, 2),
                'data' => $months->values(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $byYear,
        ]);
    }

    public function riwayatHaidMember(Request $req, $id)
    {
        $user = $req->user();

        if ($user->isMember() && $user->id != $id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $haid = DataHaid::where('id_user', $id)
            ->whereHas('user', function ($query) {
                $query->where('jk', 'P');
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $grouped = $haid->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_mulai)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_mulai)->format('m');
            })->map(function ($monthGroup, $month) {
                $monthName = Carbon::createFromFormat('m', $month)
                    ->locale('id')
                    ->translatedFormat('F');
                return [
                    'month' => strtolower($monthName),
                    'data' => $monthGroup->values(),
                ];
            })->values();
        });

        $formatted = $grouped->map(function ($months, $year) {
            return [
                'year' => (int) $year,
                'months' => $months,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $formatted
        ]);
    }

    public function listHaid(Request $req)
    {
        $u = $req->user();
        $today = Carbon::today()->toDateString();

        $rows = DataHaid::select('data_haid.*')
            ->join('users', 'users.id', '=', 'data_haid.id_user')
            ->where('data_haid.id_user', $u->id)
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->orderBy('users.nama', 'asc')
            ->with('user')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data member perempuan berhasil dimuat.',
            'data' => $rows
        ]);
    }

    public function storeHaid(Request $req)
    {
        $u = $req->user();

        $jumlahBulanIni = DB::table('data_haid')
        ->where('id_user', $u->id)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

        if ($jumlahBulanIni >= 2) {
            return response()->json([
                'status' => false,
                'message' => 'Anda hanya dapat membuat maksimal 2 data siklus haid dalam 1 bulan.'
            ], 400);
        }

        $data = $req->validate([
            'tanggal_mulai' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        if (DB::table('data_haid')
            ->where('id_user', $u->id)
            ->where('status', 'berlangsung')
            ->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda masih memiliki data haid yang berlangsung. Selesaikan terlebih dahulu.'
            ], 400);
        }

        DB::table('data_haid')->insert([
            'id_user' => $u->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => null,
            'durasi_hari' => 0,
            'status' => 'berlangsung',
            'catatan' => $data['catatan'] ?? 'Data haid dari aplikasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => true, 'message' => 'Data haid berhasil dimulai']);
    }

    public function updateHaid(Request $req, $id)
    {
        $u = $req->user();
        
        $haid = DB::table('data_haid')
            ->where('id', $id)
            ->where('id_user', $u->id)
            ->first();

        if (!$haid) {
            return response()->json(['status' => false, 'message' => 'Data haid tidak ditemukan'], 404);
        }

        if ($haid->status === 'selesai') {
            return response()->json(['status' => false, 'message' => 'Data haid sudah selesai'], 400);
        }

        $tanggalSelesai = date('Y-m-d');
        $tanggalMulai = date('Y-m-d', strtotime($haid->tanggal_mulai));
        $durasiHari = max(1, (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / 86400 + 1);

        DB::table('data_haid')
            ->where('id', $id)
            ->update([
                'tanggal_selesai' => $tanggalSelesai,
                'durasi_hari' => $durasiHari,
                'status' => 'selesai',
                'updated_at' => now(),
            ]);

        DB::table('peminjaman_pita')
            ->where('id_user', $u->id)
            ->whereNull('tanggal_kembali')
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->update([
                'estimasi_selesai_haid' => date('Y-m-d', strtotime($tanggalSelesai . ' +1 day')),
                'updated_at' => now(),
            ]);

        return response()->json(['status' => true, 'message' => 'Data haid berhasil diselesaikan']);
    }
       
    // public function storePita(Request $req) {
    //     $u = $req->user();
        
    //     $activePinjaman = DB::table('peminjaman_pita')
    //         ->where('id_user', $u->id)
    //         ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
    //         ->first();

    //     if ($activePinjaman) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Anda masih memiliki peminjaman yang belum dikembalikan'
    //         ], 400);
    //     }

    //     $activeHaid = DB::table('data_haid')
    //         ->where('id_user', $u->id)
    //         ->where('status', 'berlangsung')
    //         ->first();

    //     if (!$activeHaid) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Tidak ada data haid yang aktif. Mulai jadwal haid terlebih dahulu.'
    //         ], 400);
    //     }

    //     $data = $req->validate([
    //         'jumlah_pita' => 'required|integer|min:1',
    //         'tanggal_pinjam' => 'required|date',
    //     ]);

    //     DB::table('peminjaman_pita')->insert([
    //         'id_user' => $u->id,
    //         'jumlah_pita' => 1,
    //         'tanggal_pinjam' => $data['tanggal_pinjam'],
    //         'tanggal_kembali' => null,
    //         'status' => 'menunggu',
    //         'keterangan' => 'Peminjaman pita: 1 buah',
    //         'estimasi_selesai_haid' => $dataHaid = DataHaid::where('id_user', $u->id)->latest()->first() ? $dataHaid->estimasi_mulai->addDays(7)->format('Y-m-d') : Carbon::parse(now())->addDays(7)->format('Y-m-d'),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     return response()->json(['status' => true, 'message' => 'Pengajuan peminjaman pita berhasil']);
    // }

    // public function getRiwayatPita($userId)
    // {
    //     $u = request()->user();
        
    //     // if ($u->id != $userId && (!$u->isHealthMonitor() || !$u->isMember())) {
    //     //     return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
    //     // }

    //     $data = DB::table('peminjaman_pita as p')
    //         ->leftJoin('data_haid as h', 'p.id_user', '=', 'h.id_user')
    //         ->where('p.id_user', $userId)
    //         ->select(
    //             'p.*',
    //             'h.tanggal_mulai',
    //             'h.tanggal_selesai as estimasi_selesai'
    //         )
    //         ->orderBy('p.created_at', 'desc')
    //         ->get();

    //     // Group by year and month
    //     $grouped = [];
    //     foreach ($data as $item) {
    //         $date = date('Y-m-d', strtotime($item->tanggal_pinjam));
    //         $year = date('Y', strtotime($date));
    //         $month = date('F', strtotime($date));
            
    //         if (!isset($grouped[$year])) {
    //             $grouped[$year] = ['year' => (int)$year, 'months' => []];
    //         }
            
    //         $monthKey = strtolower($month);
    //         if (!isset($grouped[$year]['months'][$monthKey])) {
    //             $grouped[$year]['months'][$monthKey] = ['month' => $monthKey, 'data' => []];
    //         }
            
    //         $grouped[$year]['months'][$monthKey]['data'][] = $item;
    //     }

    //     $result = [];
    //     foreach ($grouped as $year => $yearData) {
    //         $yearData['months'] = array_values($yearData['months']);
    //         $result[] = $yearData;
    //     }

    //     return response()->json(['status' => true, 'data' => $result]);
    // }

    public function checkPeminjamanStatus(Request $req)
    {
        $u = $req->user();
        $today = Carbon::today();
        
        // Auto-update status jadi terlambat jika melewati estimasi
        DB::table('peminjaman_pita')
            ->where('id_user', $u->id)
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->whereNull('tanggal_kembali')
            ->whereDate('estimasi_selesai_haid', '<', $today)
            ->update([
                'status' => 'terlambat',
                'updated_at' => now()
            ]);
        
        // Ambil peminjaman aktif
        $activePinjaman = DB::table('peminjaman_pita')
            ->where('id_user', $u->id)
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->whereNull('tanggal_kembali')
            ->first();
        
        if (!$activePinjaman) {
            return response()->json([
                'status' => true,
                'data' => null
            ]);
        }
        
        $estimasi = Carbon::parse($activePinjaman->estimasi_selesai_haid);
        $diff = $today->diffInDays($estimasi, false); // false = bisa negatif
        
        $notification = null;
        
        // Jika hari ini adalah hari estimasi selesai
        if ($diff === 0) {
            $notification = [
                'type' => 'warning',
                'title' => 'Pengembalian Pita Hari Ini!',
                'message' => 'Hari ini adalah estimasi terakhir pengembalian pita. Segera kembalikan pita Anda.',
                'days_remaining' => 0
            ];
        }
        // Jika 1-3 hari sebelum estimasi selesai
        elseif ($diff > 0 && $diff <= 3) {
            $notification = [
                'type' => 'info',
                'title' => 'Pengingat Pengembalian Pita',
                'message' => "Estimasi pengembalian pita Anda adalah {$diff} hari lagi. Harap kembalikan tepat waktu.",
                'days_remaining' => $diff
            ];
        }
        // Jika sudah melewati estimasi (terlambat)
        elseif ($diff < 0) {
            $lateDays = abs($diff);
            $notification = [
                'type' => 'danger',
                'title' => 'Terlambat Mengembalikan Pita!',
                'message' => "Anda sudah terlambat {$lateDays} hari mengembalikan pita. Segera kembalikan!",
                'days_late' => $lateDays
            ];
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'peminjaman' => $activePinjaman,
                'notification' => $notification,
                'estimasi_selesai' => $estimasi->format('Y-m-d'),
                'days_diff' => $diff
            ]
        ]);
    }

    public function getRiwayatPita($userId)
    {
        $u = request()->user();
        
        $data = DB::table('peminjaman_pita as p')
            ->where('p.id_user', $userId)
            ->select(
                'p.*'
            )
            ->orderBy('p.created_at', 'desc')
            ->get();

        // Group by year and month
        $grouped = [];
        foreach ($data as $item) {
            $date = date('Y-m-d', strtotime($item->tanggal_pinjam));
            $year = date('Y', strtotime($date));
            $month = date('F', strtotime($date));
            
            if (!isset($grouped[$year])) {
                $grouped[$year] = ['year' => (int)$year, 'months' => []];
            }
            
            $monthKey = strtolower($month);
            if (!isset($grouped[$year]['months'][$monthKey])) {
                $grouped[$year]['months'][$monthKey] = ['month' => $monthKey, 'data' => []];
            }
            
            $grouped[$year]['months'][$monthKey]['data'][] = $item;
        }

        $result = [];
        foreach ($grouped as $year => $yearData) {
            $yearData['months'] = array_values($yearData['months']);
            $result[] = $yearData;
        }

        return response()->json(['status' => true, 'data' => $result]);
    }

    public function storePita(Request $req) {
        $u = $req->user();
        
        $activePinjaman = DB::table('peminjaman_pita')
            ->where('id_user', $u->id)
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->first();

        if ($activePinjaman) {
            return response()->json([
                'status' => false,
                'message' => 'Anda masih memiliki peminjaman yang belum dikembalikan'
            ], 400);
        }

        $activeHaid = DB::table('data_haid')
            ->where('id_user', $u->id)
            ->where('status', 'berlangsung')
            ->first();

        if (!$activeHaid) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data haid yang aktif. Mulai jadwal haid terlebih dahulu.'
            ], 400);
        }

        $data = $req->validate([
            'jumlah_pita' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
        ]);

        // Hitung estimasi selesai: tanggal mulai haid + 7 hari
        $estimasiSelesai = Carbon::parse($activeHaid->tanggal_mulai)->addDays(7)->format('Y-m-d');

        DB::table('peminjaman_pita')->insert([
            'id_user' => $u->id,
            'jumlah_pita' => 1,
            'tanggal_pinjam' => $data['tanggal_pinjam'],
            'tanggal_kembali' => null,
            'status' => 'menunggu',
            'verified' => 0,
            'keterangan' => 'Peminjaman pita: 1 buah',
            'estimasi_selesai_haid' => $estimasiSelesai,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => true, 'message' => 'Pengajuan peminjaman pita berhasil']);
    }

    public function getStatusPita(Request $req) {
        $u = $req->user();
        $pita = PeminjamanPita::where('id_user', $u->id)->orderByDesc('created_at', 'DESC')->first();
        $status = true;
        $statusText = 'Tidak ada peminjaman aktif';

        if ($pita->status === 'menunggu' && !$pita->verified) {
            $status = false;
            $statusText = 'Menunggu verifikasi dari Health Monitor';
        } elseif ($pita->status === 'dipinjam' && $pita->verified) {
            $status = false;
            $statusText = 'Pita sedang dipinjam';
        } elseif ($pita->status === 'dikembalikan') {
            $status = true;
            $statusText = 'Pita telah dikembalikan';
        } else {
            $status = false;
            $statusText = 'Tidak ada peminjaman aktif';
        }

        return response()->json([
            'status' => $status,
            'status_text' => $statusText
        ]);
    }
}
