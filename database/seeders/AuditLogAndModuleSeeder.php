<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use App\Models\GlobalModuleConfig;
use App\Models\User;
use App\Models\Instansi;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuditLogAndModuleSeeder extends Seeder
{
    public function run()
    {
        // 1. Inisialisasi Master Modul Global jika belum ada
        $catalog = GlobalModuleConfig::getModuleCatalog();
        foreach ($catalog as $key => $meta) {
            GlobalModuleConfig::firstOrCreate(
                ['module_key' => $key],
                [
                    'name' => $meta['name'],
                    'status' => 'active',
                    'maintenance_message' => null,
                    'tester_user_ids' => null,
                ]
            );
        }

        // Tandai 1 modul sebagai contoh maintenance dan 1 modul sebagai tester only jika belum diatur
        $burnout = GlobalModuleConfig::where('module_key', 'burnout')->first();
        if ($burnout && $burnout->status === 'active') {
            $burnout->update([
                'status' => 'maintenance',
                'maintenance_message' => 'Modul Skrining Burnout sedang dalam masa kalibrasi algoritma kuesioner DASS-21.',
            ]);
        }

        // 2. Ambil user & instansi sample
        $superadmin = User::where('level', 'Superadmin')->first();
        $adminInstansi = User::where('level', 'Admin Instansi')->first();
        $member = User::where('level', 'Member')->first();
        $instansi = Instansi::first();

        // 3. Seed Audit Logs jika masih kosong atau sedikit
        if (AuditLog::count() < 10) {
            $sampleLogs = [
                // Day 0 (Hari Ini)
                [
                    'days_ago' => 0,
                    'category' => 'auth',
                    'action' => 'web_login_password',
                    'description' => 'Super Administrator berhasil login via Web menggunakan kredensial terverifikasi.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['platform' => 'web', 'browser' => 'Chrome 122', 'role' => 'Superadmin'],
                ],
                [
                    'days_ago' => 0,
                    'category' => 'module',
                    'action' => 'module_config_updated',
                    'description' => 'Super Administrator memperbarui status Master Modul & Fitur global sistem.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['module' => 'burnout', 'status' => 'maintenance'],
                ],
                [
                    'days_ago' => 0,
                    'category' => 'auth',
                    'action' => 'mobile_login_google',
                    'description' => 'Pengguna ' . ($member ? $member->name : 'Siswa') . ' berhasil login ke Mobile App via Google Sign-In.',
                    'user' => $member,
                    'ip' => '114.122.38.10',
                    'properties' => ['platform' => 'mobile', 'device' => 'Samsung Galaxy A54'],
                ],
                // Day 1
                [
                    'days_ago' => 1,
                    'category' => 'billing',
                    'action' => 'payment_google_pay',
                    'description' => 'Pembayaran Google Pay berhasil untuk institusi ' . ($instansi ? $instansi->nama_instansi : 'SMK Telesandi') . ' (Order: GPA.3042-9182-3810) senilai Rp 10.656.000',
                    'user' => $adminInstansi,
                    'ip' => '180.252.164.22',
                    'properties' => ['nominal' => 10656000, 'mode' => 'perpanjang', 'kuota_akhir' => 800],
                ],
                [
                    'days_ago' => 1,
                    'category' => 'auth',
                    'action' => 'web_logout',
                    'description' => 'Pengguna mengakhiri sesi dan logout dari sistem web.',
                    'user' => $adminInstansi,
                    'ip' => '180.252.164.22',
                    'properties' => ['session_duration_minutes' => 45],
                ],
                // Day 2
                [
                    'days_ago' => 2,
                    'category' => 'maintenance',
                    'action' => 'maintenance_mode_toggled',
                    'description' => 'Mode pemeliharaan sistem (Mobile Application) diaktifkan oleh Super Administrator untuk pembaruan patch v2.4.0.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['platform' => 'mobile', 'target_version' => '2.4.0'],
                ],
                [
                    'days_ago' => 2,
                    'category' => 'maintenance',
                    'action' => 'maintenance_mode_toggled',
                    'description' => 'Mode pemeliharaan sistem (Mobile Application) dinonaktifkan kembali setelah verifikasi patch v2.4.0.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['platform' => 'mobile', 'status' => 'resolved'],
                ],
                // Day 3
                [
                    'days_ago' => 3,
                    'category' => 'user',
                    'action' => 'tester_status_toggled',
                    'description' => 'Pengguna ' . ($member ? $member->name : 'Siswa') . ' ditetapkan sebagai Akun Tester Resmi untuk pengujian modul pra-rilis.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['target_user_id' => $member ? $member->id : null, 'status' => 'tester_enabled'],
                ],
                [
                    'days_ago' => 3,
                    'category' => 'auth',
                    'action' => 'web_login_password',
                    'description' => 'Admin Instansi masuk ke portal manajemen institusi.',
                    'user' => $adminInstansi,
                    'ip' => '180.252.164.22',
                    'properties' => ['platform' => 'web'],
                ],
                // Day 4
                [
                    'days_ago' => 4,
                    'category' => 'module',
                    'action' => 'instansi_modules_updated',
                    'description' => 'Admin Instansi memperbarui konfigurasi modul & perizinan institusi.',
                    'user' => $adminInstansi,
                    'ip' => '180.252.164.22',
                    'properties' => ['active_modules_count' => 8],
                ],
                // Day 5
                [
                    'days_ago' => 5,
                    'category' => 'auth',
                    'action' => 'mobile_login_password',
                    'description' => 'Pengguna berhasil login ke Mobile App Android.',
                    'user' => $member,
                    'ip' => '114.122.38.10',
                    'properties' => ['platform' => 'mobile'],
                ],
                // Day 6
                [
                    'days_ago' => 6,
                    'category' => 'user',
                    'action' => 'user_suspended',
                    'description' => 'SuperAdmin men-suspend akun pengguna karena terindikasi penyalahgunaan spam.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['duration_days' => 7, 'reason' => 'Penyalahgunaan data vitals'],
                ],
                // Day 7
                [
                    'days_ago' => 7,
                    'category' => 'billing',
                    'action' => 'payment_google_play_inapp',
                    'description' => 'Pembayaran In-App Google Play berhasil untuk langganan tambahan kuota 50 pengguna.',
                    'user' => $adminInstansi,
                    'ip' => '180.252.164.22',
                    'properties' => ['quota_added' => 50, 'product_id' => 'quota_50'],
                ],
                // Day 8
                [
                    'days_ago' => 8,
                    'category' => 'auth',
                    'action' => 'web_login_google',
                    'description' => 'Pengguna login via SSO Google Web.',
                    'user' => $member,
                    'ip' => '36.85.120.44',
                    'properties' => ['platform' => 'web'],
                ],
                // Day 10
                [
                    'days_ago' => 10,
                    'category' => 'auth',
                    'action' => 'web_login_password',
                    'description' => 'SuperAdmin login ke Dashboard Sistem.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['platform' => 'web'],
                ],
                // Day 12
                [
                    'days_ago' => 12,
                    'category' => 'system',
                    'action' => 'system_bootstrapped',
                    'description' => 'Inisialisasi sistem audit logging dan modul manajemen terpadu AkuSehat.',
                    'user' => $superadmin,
                    'ip' => '127.0.0.1',
                    'properties' => ['version' => '2.5.0-L13'],
                ],
            ];

            foreach ($sampleLogs as $data) {
                $u = $data['user'];
                $time = Carbon::now()->subDays($data['days_ago'])->subHours(rand(1, 10))->subMinutes(rand(5, 50));
                
                AuditLog::create([
                    'user_id' => $u ? $u->id : null,
                    'user_name' => $u ? $u->name : 'Sistem Ekosistem',
                    'user_email' => $u ? $u->email : 'system@akusehat.id',
                    'user_role' => $u ? $u->level : 'Superadmin',
                    'instansi_id' => ($u && $u->instansi_id) ? $u->instansi_id : ($instansi ? $instansi->id : null),
                    'instansi_name' => ($u && $u->instansi) ? $u->instansi->nama_instansi : ($instansi ? $instansi->nama_instansi : 'AkuSehat Global'),
                    'category' => $data['category'],
                    'action' => $data['action'],
                    'description' => $data['description'],
                    'properties' => $data['properties'],
                    'ip_address' => $data['ip'],
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122.0.0.0',
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }
}
