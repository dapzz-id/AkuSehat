<?php

namespace App\Modules\User\Member\Repositories;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Modules\User\Enums\UserRole;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BmiStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberRepository
{
    public function findById(int|string $id): ?User
    {
        return User::with(['divisi', 'instansi'])->find($id);
    }

    public function getUserDivisi(?string $divisiId)
    {
        if (!$divisiId) {
            return null;
        }
        return DB::table('divisi')->where('id', $divisiId)->first();
    }

    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function updatePassword(User $user, string $hashedPassword): bool
    {
        $user->password = $hashedPassword;
        return $user->save();
    }

    public function getLatestKesehatan(string|int $userId)
    {
        return DB::table('kesehatan')->where('id_user', $userId)->orderByDesc('tgl')->first();
    }

    public function getLatestHb(string|int $userId)
    {
        return DB::table('hb')->where('id_user', $userId)->orderByDesc('tgl')->value('hb');
    }

    public function getHealthConsultantDashboardStats(): array
    {
        $totalMember = User::where('level', UserRole::MEMBER->value)->count();
        $totalKelas = Kelas::count();

        $statistikIMT = Kesehatan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $dataKelas = Kelas::with(['users' => function($query) {
            $query->where('level', UserRole::MEMBER->value);
        }])->get()->map(function($kelas) {
            $memberIds = $kelas->users->pluck('id');

            $kesehatanTerbaru = Kesehatan::whereIn('id_user', $memberIds)
                ->select('id_user', 'status')
                ->whereIn('id_kesehatan', function($query) use ($memberIds) {
                    $query->select(DB::raw('MAX(id_kesehatan)'))
                        ->from('kesehatan')
                        ->whereIn('id_user', $memberIds)
                        ->groupBy('id_user');
                })
                ->get()
                ->groupBy('status');

            return [
                'id' => $kelas->id,
                'kelas' => $kelas->kelas,
                'jurusan' => $kelas->jurusan,
                'total_member' => $kelas->users->count(),
                'statistik' => [
                    'normal' => $kesehatanTerbaru->get(BmiStatus::NORMAL->value, collect())->count(),
                    'kurus' => $kesehatanTerbaru->get(BmiStatus::KURUS->value, collect())->count(),
                    'overweight' => $kesehatanTerbaru->get(BmiStatus::GEMUK->value, collect())->count(),
                    'obesitas' => $kesehatanTerbaru->get(BmiStatus::OBESITAS->value, collect())->count(),
                ]
            ];
        });

        return [
            'statistik_umum' => [
                'total_member' => $totalMember,
                'total_kelas' => $totalKelas,
                'statistik_imt' => $statistikIMT,
            ],
            'data_kelas' => $dataKelas,
        ];
    }

    public function getMemberDetailDashboard(int|string $memberId): ?array
    {
        $member = User::with('kelas')->find($memberId);
        if (!$member || $member->level !== UserRole::MEMBER->value) {
            return null;
        }

        $kesehatanTerbaru = Kesehatan::where('id_user', $memberId)
            ->orderBy('tgl', 'desc')
            ->first();

        $hbTerbaru = Hb::where('id_user', $memberId)
            ->orderBy('tgl', 'desc')
            ->first();

        $riwayatKesehatan = Kesehatan::where('id_user', $memberId)
            ->orderBy('tgl', 'desc')
            ->take(5)
            ->get();

        $riwayatHb = Hb::where('id_user', $memberId)
            ->orderBy('tgl', 'desc')
            ->take(5)
            ->get();

        return [
            'member' => $member,
            'kesehatan_terbaru' => $kesehatanTerbaru,
            'hb_terbaru' => $hbTerbaru,
            'riwayat_kesehatan' => $riwayatKesehatan,
            'riwayat_hb' => $riwayatHb,
        ];
    }

    public function getHealthRecordsFor5Years(string|int $userId)
    {
        return DB::table('kesehatan')
            ->select(
                DB::raw("EXTRACT(YEAR FROM tgl) as year"),
                DB::raw("EXTRACT(MONTH FROM tgl) as month"),
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
            ->where('id_user', $userId)
            ->where('tgl', '>=', now()->subYears(5)->startOfYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }
}
