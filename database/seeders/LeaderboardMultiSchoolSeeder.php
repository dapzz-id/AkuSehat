<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LeaderboardMultiSchoolSeeder extends Seeder
{
    public function run()
    {
        $this->command?->info("Starting LeaderboardMultiSchoolSeeder...");

        // 1. Definition of 5 Diverse Schools
        $schoolsData = [
            'telesandi' => [
                'nama' => 'SMK TELEKOMUNIKASI TELESANDI BEKASI',
                'jenis' => 'Sekolah',
                'jenjang' => 'SMK',
                'npsn' => '20237981',
                'invite_code' => 'TELESANDI2024',
                'kota' => 'Bekasi',
                'provinsi' => 'Jawa Barat',
                'divisi' => ['XI TKJ 2', 'XII TKJ 1', 'X RPL 1'],
            ],
            'sman1jkt' => [
                'nama' => 'SMA NEGERI 1 JAKARTA',
                'jenis' => 'Sekolah',
                'jenjang' => 'SMA',
                'npsn' => '20107293',
                'invite_code' => 'SMAN1JKT',
                'kota' => 'Jakarta Pusat',
                'provinsi' => 'DKI Jakarta',
                'divisi' => ['X MIPA 1', 'XI MIPA 2', 'XII MIPA 1'],
            ],
            'taruna' => [
                'nama' => 'SMA TARUNA NUSANTARA MAGELANG',
                'jenis' => 'Sekolah',
                'jenjang' => 'SMA',
                'npsn' => '20328221',
                'invite_code' => 'TARUNANUS24',
                'kota' => 'Magelang',
                'provinsi' => 'Jawa Tengah',
                'divisi' => ['Graha 1 (X MIPA)', 'Graha 3 (XI MIPA)', 'Graha 5 (XII MIPA)'],
            ],
            'sman3bdg' => [
                'nama' => 'SMA NEGERI 3 BANDUNG',
                'jenis' => 'Sekolah',
                'jenjang' => 'SMA',
                'npsn' => '20219234',
                'invite_code' => 'SMAN3BDG',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'divisi' => ['X IPS 1', 'XI MIPA 3', 'XII MIPA 1'],
            ],
            'smkn2sby' => [
                'nama' => 'SMK NEGERI 2 SURABAYA',
                'jenis' => 'Sekolah',
                'jenjang' => 'SMK',
                'npsn' => '20532219',
                'invite_code' => 'SMKN2SBY',
                'kota' => 'Surabaya',
                'provinsi' => 'Jawa Timur',
                'divisi' => ['X TKJ 1', 'XI RPL 2', 'XII Multimedia'],
            ],
        ];

        $instansiModels = [];
        $divisiModels = [];

        foreach ($schoolsData as $key => $s) {
            // Find or create LicenseKey
            $license = LicenseKey::firstOrCreate(
                ['key' => 'LIC-' . strtoupper($s['invite_code'])],
                [
                    'kuota_pengguna' => 500,
                    'status' => LicenseKey::STATUS_ACTIVE,
                    'tanggal_berakhir' => now()->addDays(365),
                ]
            );

            // Find or create Instansi
            $instansi = Instansi::withoutGlobalScopes()->where('nama_instansi', $s['nama'])->first();
            if (!$instansi) {
                $instansi = Instansi::create([
                    'nama_instansi' => $s['nama'],
                    'jenis_instansi' => $s['jenis'],
                    'jenjang' => $s['jenjang'],
                    'npsn' => $s['npsn'],
                    'invite_code' => $s['invite_code'],
                    'kota' => $s['kota'],
                    'provinsi' => $s['provinsi'],
                    'alamat' => "Jl. Pendidikan No. " . rand(1, 99) . ", {$s['kota']}",
                    'license_id_active' => $license->id,
                ]);
            }
            $license->update(['instansi_id' => $instansi->id]);
            $instansiModels[$key] = $instansi;

            // Create Divisi
            $divisiModels[$key] = [];
            foreach ($s['divisi'] as $divName) {
                $div = Divisi::firstOrCreate(
                    [
                        'instansi_id' => $instansi->id,
                        'divisi_name' => $divName,
                    ],
                    [
                        'tgl' => '2024-01-01',
                        'kategori' => Divisi::KATEGORI_MEMBER,
                        'color_cover' => '#059669',
                    ]
                );
                $divisiModels[$key][$divName] = $div;
            }
        }

        // 2. Definition of Core Runners across Schools
        // Calibrated to give a healthy, diverse podium in Global Leaderboard
        $runnersConfig = [
            // 🥇 Rank 1 Global: SMA Taruna Nusantara
            [
                'school_key' => 'taruna',
                'div_name' => 'Graha 5 (XII MIPA)',
                'username' => 'arya_sena',
                'name' => 'Arya Sena Bagaskara',
                'jk' => 'L',
                'target_km_month' => 68.4,
                'pace_sec' => 272, // ~4:32/km
                'runs_month' => 9,
            ],
            // 🥈 Rank 2 Global: SMK Telekomunikasi Telesandi
            [
                'school_key' => 'telesandi',
                'div_name' => 'XII TKJ 1',
                'username' => 'gita_wijaya',
                'name' => 'Gita Wijaya',
                'jk' => 'P',
                'target_km_month' => 58.2,
                'pace_sec' => 285, // ~4:45/km
                'runs_month' => 8,
            ],
            // 🥉 Rank 3 Global: SMA Negeri 1 Jakarta
            [
                'school_key' => 'sman1jkt',
                'div_name' => 'XII MIPA 1',
                'username' => 'nabila_putri',
                'name' => 'Nabila Putri Andini',
                'jk' => 'P',
                'target_km_month' => 52.7,
                'pace_sec' => 292, // ~4:52/km
                'runs_month' => 8,
            ],
            // #4 Global: SMA Negeri 3 Bandung
            [
                'school_key' => 'sman3bdg',
                'div_name' => 'XI MIPA 3',
                'username' => 'farhan_alamsyah',
                'name' => 'Farhan Alamsyah',
                'jk' => 'L',
                'target_km_month' => 46.1,
                'pace_sec' => 305, // ~5:05/km
                'runs_month' => 7,
            ],
            // #5 Global: SMK Negeri 2 Surabaya
            [
                'school_key' => 'smkn2sby',
                'div_name' => 'XI RPL 2',
                'username' => 'dimas_anggara',
                'name' => 'Dimas Anggara',
                'jk' => 'L',
                'target_km_month' => 41.5,
                'pace_sec' => 315, // ~5:15/km
                'runs_month' => 6,
            ],
            // #6 Global: SMK Telekomunikasi Telesandi
            [
                'school_key' => 'telesandi',
                'div_name' => null, // Umum / Admin
                'username' => 'admin_telesandi',
                'name' => 'Admin Sekolah',
                'jk' => 'L',
                'target_km_month' => 36.8,
                'pace_sec' => 320, // ~5:20/km
                'runs_month' => 6,
            ],
            // #7 Global: SMA Taruna Nusantara
            [
                'school_key' => 'taruna',
                'div_name' => 'Graha 3 (XI MIPA)',
                'username' => 'bagus_wicaksono',
                'name' => 'Bagus Wicaksono',
                'jk' => 'L',
                'target_km_month' => 32.4,
                'pace_sec' => 330, // ~5:30/km
                'runs_month' => 5,
            ],
            // #8 Global: SMA Negeri 1 Jakarta
            [
                'school_key' => 'sman1jkt',
                'div_name' => 'XI MIPA 2',
                'username' => 'anindya_maharani',
                'name' => 'Anindya Maharani',
                'jk' => 'P',
                'target_km_month' => 27.9,
                'pace_sec' => 338, // ~5:38/km
                'runs_month' => 5,
            ],
            // #9 Global: SMK Telekomunikasi Telesandi (User Fajar Putri!)
            [
                'school_key' => 'telesandi',
                'div_name' => 'XI TKJ 2',
                'username' => 'member1',
                'name' => 'Fajar Putri',
                'jk' => 'P',
                'target_km_month' => 24.5,
                'pace_sec' => 342, // ~5:42/km
                'runs_month' => 5,
            ],
            // #10 Global: SMA Negeri 3 Bandung
            [
                'school_key' => 'sman3bdg',
                'div_name' => 'XII MIPA 1',
                'username' => 'rizky_pradana',
                'name' => 'Rizky Pradana',
                'jk' => 'L',
                'target_km_month' => 21.0,
                'pace_sec' => 355, // ~5:55/km
                'runs_month' => 4,
            ],
            // #11: SMK Negeri 2 Surabaya
            [
                'school_key' => 'smkn2sby',
                'div_name' => 'XII Multimedia',
                'username' => 'tiara_andini',
                'name' => 'Tiara Andini',
                'jk' => 'P',
                'target_km_month' => 18.2,
                'pace_sec' => 365,
                'runs_month' => 4,
            ],
            // #12: SMA Taruna Nusantara
            [
                'school_key' => 'taruna',
                'div_name' => 'Graha 1 (X MIPA)',
                'username' => 'dhani_satria',
                'name' => 'Dhani Satria',
                'jk' => 'L',
                'target_km_month' => 15.6,
                'pace_sec' => 375,
                'runs_month' => 3,
            ],
            // #13: SMA Negeri 1 Jakarta
            [
                'school_key' => 'sman1jkt',
                'div_name' => 'X MIPA 1',
                'username' => 'reza_firmansyah',
                'name' => 'Reza Firmansyah',
                'jk' => 'L',
                'target_km_month' => 13.0,
                'pace_sec' => 385,
                'runs_month' => 3,
            ],
            // #14: SMK Telekomunikasi Telesandi
            [
                'school_key' => 'telesandi',
                'div_name' => 'X RPL 1',
                'username' => 'citra_lestari',
                'name' => 'Citra Lestari',
                'jk' => 'P',
                'target_km_month' => 10.5,
                'pace_sec' => 395,
                'runs_month' => 3,
            ],
            // #15: SMA Negeri 3 Bandung
            [
                'school_key' => 'sman3bdg',
                'div_name' => 'X IPS 1',
                'username' => 'salma_safira',
                'name' => 'Salma Safira',
                'jk' => 'P',
                'target_km_month' => 8.2,
                'pace_sec' => 410,
                'runs_month' => 2,
            ],
            // #16: SMK Negeri 2 Surabaya
            [
                'school_key' => 'smkn2sby',
                'div_name' => 'X TKJ 1',
                'username' => 'bayu_segara',
                'name' => 'Bayu Segara',
                'jk' => 'L',
                'target_km_month' => 6.5,
                'pace_sec' => 425,
                'runs_month' => 2,
            ],
        ];

        // Clear existing activities to ensure clean leaderboard state
        UserActivity::truncate();

        $now = Carbon::now();
        $totalActivities = 0;

        foreach ($runnersConfig as $idx => $cfg) {
            $instansi = $instansiModels[$cfg['school_key']];
            $divId = null;
            if ($cfg['div_name'] && isset($divisiModels[$cfg['school_key']][$cfg['div_name']])) {
                $divId = $divisiModels[$cfg['school_key']][$cfg['div_name']]->id;
            }

            // Find or create user
            $user = User::where('username', $cfg['username'])->first();
            if (!$user) {
                // If Fajar Putri exists by name
                if ($cfg['name'] === 'Fajar Putri') {
                    $user = User::where('nama', 'like', '%Fajar Putri%')->first();
                }
            }

            if (!$user) {
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'id_divisi' => $divId,
                    'instansi_id' => $instansi->id,
                    'tgl' => '2024-01-01',
                    'nomor_induk' => 'NIS-' . strtoupper($cfg['school_key']) . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                    'username' => $cfg['username'],
                    'password' => Hash::make('password123'),
                    'nama' => $cfg['name'],
                    'jk' => $cfg['jk'],
                    'level' => 'Member',
                ]);
            } else {
                // Ensure correct instansi and divisi
                $user->update([
                    'instansi_id' => $instansi->id,
                    'id_divisi' => $divId ?? $user->id_divisi,
                    'nama' => $cfg['name'],
                ]);
            }

            // --- 3. SEED ACTIVITIES ---

            // A. Bulan Ini (Oktober 2026) - Strictly within now()->startOfMonth() and now()->endOfMonth()
            $runCount = $cfg['runs_month'];
            $kmPerRun = $cfg['target_km_month'] / $runCount;

            for ($r = 1; $r <= $runCount; $r++) {
                $distMeters = round(($kmPerRun + (rand(-5, 5) / 10)) * 1000);
                $durSec = round(($distMeters / 1000.0) * ($cfg['pace_sec'] + rand(-10, 10)));
                $paceMin = floor($durSec / ($distMeters / 1000.0) / 60);
                $paceSec = floor(fmod($durSec / ($distMeters / 1000.0), 60));
                $avgPace = sprintf("%02d:%02d", $paceMin, $paceSec);

                // Ensure date is strictly within current month
                $dayOfMonth = min(27, max(1, rand(1, 26)));
                $runDate = $now->copy()->day($dayOfMonth)->hour(rand(6, 17))->minute(rand(0, 59))->second(rand(0, 59));

                UserActivity::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'instansi_id' => $user->instansi_id,
                    'activity_type' => 'run',
                    'title' => 'Lari Pagi ' . $runDate->format('d M'),
                    'distance_meters' => $distMeters,
                    'duration_seconds' => $durSec,
                    'calories' => round(($distMeters / 1000) * 62),
                    'avg_pace' => $avgPace,
                    'steps' => round(($distMeters / 1000) * 1350),
                    'started_at' => $runDate,
                    'finished_at' => $runDate->copy()->addSeconds($durSec),
                ]);
                $totalActivities++;
            }

            // B. Tahun Ini (Earlier months in 2026: Jan - Sep) - Strictly in current year
            $yearlyMonths = rand(3, 6);
            for ($m = 1; $m <= $yearlyMonths; $m++) {
                $distMeters = round(rand(12, 35) * 1000);
                $durSec = round(($distMeters / 1000.0) * ($cfg['pace_sec'] + rand(-15, 20)));
                $paceMin = floor($durSec / ($distMeters / 1000.0) / 60);
                $paceSec = floor(fmod($durSec / ($distMeters / 1000.0), 60));
                $avgPace = sprintf("%02d:%02d", $paceMin, $paceSec);

                $yearDate = Carbon::create($now->year, $m, rand(1, 28), rand(6, 18), rand(0, 59), 0);

                UserActivity::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'instansi_id' => $user->instansi_id,
                    'activity_type' => 'run',
                    'title' => 'Lari Rutin ' . $yearDate->format('F Y'),
                    'distance_meters' => $distMeters,
                    'duration_seconds' => $durSec,
                    'calories' => round(($distMeters / 1000) * 62),
                    'avg_pace' => $avgPace,
                    'steps' => round(($distMeters / 1000) * 1350),
                    'started_at' => $yearDate,
                    'finished_at' => $yearDate->copy()->addSeconds($durSec),
                ]);
                $totalActivities++;
            }

            // C. Tahun Lalu (2025) - Strictly in subYear()
            $lastYearRuns = rand(3, 7);
            for ($ly = 1; $ly <= $lastYearRuns; $ly++) {
                $distMeters = round(rand(10, 30) * 1000);
                $durSec = round(($distMeters / 1000.0) * ($cfg['pace_sec'] + rand(-10, 25)));
                $paceMin = floor($durSec / ($distMeters / 1000.0) / 60);
                $paceSec = floor(fmod($durSec / ($distMeters / 1000.0), 60));
                $avgPace = sprintf("%02d:%02d", $paceMin, $paceSec);

                $lastYearDate = Carbon::create($now->year - 1, rand(1, 12), rand(1, 28), rand(6, 18), rand(0, 59), 0);

                UserActivity::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'instansi_id' => $user->instansi_id,
                    'activity_type' => 'run',
                    'title' => 'Arsip Lari ' . $lastYearDate->format('M Y'),
                    'distance_meters' => $distMeters,
                    'duration_seconds' => $durSec,
                    'calories' => round(($distMeters / 1000) * 62),
                    'avg_pace' => $avgPace,
                    'steps' => round(($distMeters / 1000) * 1350),
                    'started_at' => $lastYearDate,
                    'finished_at' => $lastYearDate->copy()->addSeconds($durSec),
                ]);
                $totalActivities++;
            }
        }

        // 4. Invalidate Leaderboard Cache
        Cache::increment('leaderboard_version');
        Cache::flush();

        $this->command?->info("Successfully seeded {$totalActivities} activities for 5 distinct schools!");
    }
}
