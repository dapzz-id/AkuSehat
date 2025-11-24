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
        $kelas = DB::table('kelas')->where('id', $u->id_kelas)->first();
        return response()->json([
            'status' => true,
            'data' => [
                'id' => $u->id,
                'nama' => $u->nama,
                'username' => $u->username,
                'nis' => $u->nis,
                'kelas' => $kelas->kelas ?? null,
                'jurusan' => $kelas->jurusan ?? null,
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
                        -- ✅ Kondisi sempurna (semua normal / tidak ada masalah)
                        WHEN LOWER(status_darah) = 'normal'
                            AND LOWER(status) = 'normal'
                            AND (LOWER(kondisi_telinga) = 'normal' OR kondisi_telinga IS NULL OR kondisi_telinga = '')
                            AND (LOWER(kondisi_gigi) = 'sehat' OR kondisi_gigi IS NULL OR kondisi_gigi = '')
                            AND (perilaku_beresiko IS NULL OR LOWER(perilaku_beresiko) = 'tidak ada' OR perilaku_beresiko = '')
                            AND (gangguan_reproduksi IS NULL OR LOWER(gangguan_reproduksi) = 'tidak ada' OR gangguan_reproduksi = '')
                        THEN 100

                        -- 🟢 Kondisi baik tapi tidak sempurna (1–2 nilai kurang ideal)
                        WHEN (LOWER(status_darah) = 'normal' OR LOWER(status) = 'normal')
                            AND (perilaku_beresiko IS NULL OR LOWER(perilaku_beresiko) = 'tidak ada' OR perilaku_beresiko = '')
                        THEN 85

                        -- 🟡 Kondisi mulai perlu perhatian (kurus, overweight, tekanan darah tinggi/rendah, dll)
                        WHEN LOWER(status) IN ('kurus', 'overweight')
                            OR LOWER(status_darah) IN ('tinggi', 'rendah')
                            OR (LOWER(kondisi_telinga) != 'normal' AND kondisi_telinga IS NOT NULL AND kondisi_telinga != '')
                            OR (LOWER(kondisi_gigi) != 'sehat' AND kondisi_gigi IS NOT NULL AND kondisi_gigi != '')
                        THEN 60

                        -- 🟠 Kondisi kurang baik (ada perilaku beresiko atau gangguan ringan)
                        WHEN (LOWER(perilaku_beresiko) != 'tidak ada' AND perilaku_beresiko IS NOT NULL AND perilaku_beresiko != '')
                            OR (LOWER(gangguan_reproduksi) != 'tidak ada' AND gangguan_reproduksi IS NOT NULL AND gangguan_reproduksi != '')
                        THEN 35

                        -- 🔴 Kondisi berat (obesitas, kombinasi banyak faktor buruk)
                        WHEN LOWER(status) = 'obesitas'
                            OR (LOWER(status_darah) = 'tinggi' AND (LOWER(perilaku_beresiko) != 'tidak ada' AND perilaku_beresiko IS NOT NULL AND perilaku_beresiko != ''))
                            OR (LOWER(gangguan_reproduksi) != 'tidak ada' AND gangguan_reproduksi IS NOT NULL AND gangguan_reproduksi != '')
                        THEN 20

                        -- Default / data tidak lengkap
                        ELSE 50
                    END), 0) as score
                ")
            )
            ->where('id_user', $u->id)
            ->where('tgl', '>=', now()->subYears(5)->startOfYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Buat array bulan dari Januari (5 tahun lalu) sampai Desember tahun ini
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
            'message' => 'Data siswi berhasil dimuat.',
            'data' => $rows
        ]);
    }

    public function storeHaid(Request $req) {
        $u = $req->user();
        $data = $req->validate([
            'tanggal_mulai' => 'required|date',
            'durasi_hari' => 'required|integer|min:1|max:15',
            'catatan' => 'nullable|string',
        ]);

        $tanggal_selesai = date('Y-m-d', strtotime($data['tanggal_mulai'] . ' + ' . intval($data['durasi_hari']) . ' days'));

        // Cek data duplikat tanggal mulai
        if (DB::table('data_haid')->where('id_user', $u->id)->where('tanggal_mulai', $data['tanggal_mulai'])->exists()) {
            return response()->json(['status'=>false,'message'=>'Data haid untuk tanggal tersebut sudah ada'], 400);
        }

        // Cek tabrakan dengan data lain
        if (DB::table('data_haid')->where('id_user', $u->id)->where(function($q) use ($data, $tanggal_selesai) {
            $q->whereBetween('tanggal_mulai', [$data['tanggal_mulai'], $tanggal_selesai])
            ->orWhereBetween('tanggal_selesai', [$data['tanggal_mulai'], $tanggal_selesai])
            ->orWhere(function($q2) use ($data, $tanggal_selesai) {
                $q2->where('tanggal_mulai', '<=', $data['tanggal_mulai'])
                    ->where('tanggal_selesai', '>=', $tanggal_selesai);
            });
        })->exists()) {
            return response()->json(['status'=>false,'message'=>'Data haid bertabrakan dengan data yang sudah ada'], 400);
        }

        // Cek apakah sudah melewati tanggal selesai
        $today = date('Y-m-d');
        $status = ($today > $tanggal_selesai) ? 'selesai' : 'berlangsung';

        DB::table('data_haid')->insert([
            'id_user' => $u->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $tanggal_selesai,
            'durasi_hari' => $data['durasi_hari'],
            'status' => $status,
            'catatan' => $data['catatan'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status'=>true,'message'=>'Data haid ditambahkan']);
    }

    public function storePita(Request $req) {
        $u = $req->user();
        $data = $req->validate([
            'jumlah_pita' => 'required|integer|min:1|max:5',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keterangan' => 'nullable|string',
            'estimasi_selesai_haid' => 'nullable|date',
        ]);

        PeminjamanPita::updateOrCreate([
            'id_user' => $u->id,
        ], [
            'status' => 'dipinjam',
            'tanggal_pinjam' => $data['tanggal_pinjam'],
            'tanggal_kembali' => $data['tanggal_kembali'],
            'jumlah_pita' => $data['jumlah_pita'],
            'keterangan' => $data['keterangan'] ?? null,
            'estimasi_selesai_haid' => $data['estimasi_selesai_haid'] ?? null,
            'verified' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status'=>true,'message'=>'Pengajuan peminjaman pita dibuat']);
    }

    public function getStatusPita(Request $req) {
        $u = $req->user();
        $pita = PeminjamanPita::where('id_user', $u->id)->orderByDesc('created_at', 'DESC')->first();
        $status = true;
        $statusText = 'Tidak ada peminjaman aktif';

        if ($pita->status === 'dipinjam' && !$pita->verified) {
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
