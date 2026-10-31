<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Models\MaintenanceMode;
use App\Models\SoftwareApp;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jika berjalan di environment production, hanya jalankan seeder SuperAdmin
        if (app()->environment('production')) {
            $this->command->info('Lingkungan terdeteksi sebagai PRODUCTION.');
            $this->call(ProductionSeeder::class);
            return;
        }

        // 1. License Key Aktif untuk Sekolah A
        $licenseA = LicenseKey::firstOrCreate(
            ['key' => 'FREE-TRIAL-2024'],
            [
                'kuota_pengguna' => 800,
                'status' => LicenseKey::STATUS_ACTIVE,
                'tanggal_berakhir' => now()->addDays(30)
            ]
        );
        
        // 2. Instansi A: SMK TELEKOMUNIKASI TELESANDI BEKASI
        $instansiA = Instansi::create([
            'nama_instansi' => 'SMK TELEKOMUNIKASI TELESANDI BEKASI',
            'jenis_instansi' => 'Sekolah',
            'jenjang' => 'SMK',
            'npsn' => '20237981',
            'invite_code' => 'TELESANDI2024',
            'alamat' => 'Jl. KH. Mochamad, Mekarsari, Kecamatan Tambun Selatan, Kabupaten Bekasi',
            'kota' => 'Bekasi',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '17510',
            'license_id_active' => $licenseA->id,
        ]);

        $licenseA->update(['instansi_id' => $instansiA->id]);

        // 3. Divisi untuk Sekolah A
        $divisiKelas = ['X TKJ 1', 'X TKJ 2', 'XI TKJ 1', 'XI TKJ 2', 'XII TKJ 1', 'X RPL 1', 'X RPL 2', 'XI RPL 1'];
        $createdDivisiA = [];
        foreach ($divisiKelas as $name) {
            $createdDivisiA[] = Divisi::create([
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'divisi_name' => $name,
                'kategori' => Divisi::KATEGORI_MEMBER,
                'color_cover' => '#2e7d32',
            ]);
        }

        // Divisi Dewan Guru & Pimpinan Sekolah A
        $divisiGuruA = Divisi::create([
            'instansi_id' => $instansiA->id,
            'tgl' => '2024-01-01',
            'divisi_name' => 'Dewan Guru & Tendik',
            'kategori' => Divisi::KATEGORI_STAF,
            'color_cover' => '#1e88e5',
        ]);

        $divisiPimpinanA = Divisi::create([
            'instansi_id' => $instansiA->id,
            'tgl' => '2024-01-01',
            'divisi_name' => 'Pimpinan & Yayasan',
            'kategori' => Divisi::KATEGORI_PIMPINAN,
            'color_cover' => '#8e24aa',
        ]);

        // 4. Users untuk Sekolah A
        $usersA = [
            [
                'id_divisi' => null,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'ADM-001',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'nama' => 'Admin Sekolah',
                'level' => 'Admin',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'HM-001',
                'username' => 'healthmonitor',
                'password' => Hash::make('password123'),
                'nama' => 'Petugas UKS',
                'level' => 'Health Monitor',
                'jk' => 'P'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'HC-001',
                'username' => 'healthconsultant',
                'password' => Hash::make('password123'),
                'nama' => 'Konsultan Kesehatan',
                'level' => 'Health Consultant',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => null, // SuperAdmin tidak terikat instansi
                'tgl' => '2024-01-01',
                'nomor_induk' => 'SA-001',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'nama' => 'Super Administrator',
                'level' => 'SuperAdmin',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'AI-001',
                'username' => 'admininstansi',
                'password' => Hash::make('password123'),
                'nama' => 'Admin Instansi Telesandi',
                'level' => 'Admin Instansi',
                'jk' => 'P'
            ],
            [
                'id_divisi' => $divisiGuruA->id,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'GURU-001',
                'username' => 'gurubudi',
                'password' => Hash::make('password123'),
                'nama' => 'Budi Santoso, S.Pd',
                'level' => 'Member',
                'jk' => 'L'
            ],
            [
                'id_divisi' => $divisiPimpinanA->id,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'KEPSEK-001',
                'username' => 'kepsek',
                'password' => Hash::make('password123'),
                'nama' => 'Drs. H. Hendra Wijaya, M.M.',
                'level' => 'Member',
                'jk' => 'L'
            ]
        ];

        foreach ($usersA as $u) {
            User::create($u);
        }

        // Sample Siswa Sekolah A
        $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko'];
        $lastNames = ['Pratama', 'Sari', 'Putra', 'Putri', 'Wijaya', 'Kusuma', 'Handoko', 'Lestari'];
        
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $gender = ($i % 2 == 0) ? 'P' : 'L';
            $randomDivisi = $createdDivisiA[array_rand($createdDivisiA)];
            
            User::create([
                'id_divisi' => $randomDivisi->id,
                'instansi_id' => $instansiA->id,
                'tgl' => '2024-01-01',
                'nomor_induk' => 'NIS-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'username' => 'member' . $i,
                'password' => Hash::make('password123'),
                'nama' => $firstName . ' ' . $lastName,
                'jk' => $gender,
                'level' => 'Member',
            ]);
        }

        // 5. Instansi B (Sekolah B: SMA NEGERI 1 JAKARTA) untuk pengujian isolasi multi-tenant
        $licenseB = LicenseKey::create([
            'key' => 'SMAN1-TRIAL-2024',
            'kuota_pengguna' => 500,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addDays(30)
        ]);

        $instansiB = Instansi::create([
            'nama_instansi' => 'SMA NEGERI 1 JAKARTA',
            'jenis_instansi' => 'Sekolah',
            'jenjang' => 'SMA',
            'npsn' => '20107293',
            'invite_code' => 'SMAN1JKT',
            'alamat' => 'Jl. Budi Utomo No.7, Ps. Baru, Sawah Besar, Kota Jakarta Pusat',
            'kota' => 'Jakarta Pusat',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '10710',
            'license_id_active' => $licenseB->id,
        ]);

        $licenseB->update(['instansi_id' => $instansiB->id]);

        $divisiB = Divisi::create([
            'instansi_id' => $instansiB->id,
            'tgl' => '2024-01-01',
            'divisi_name' => 'XII MIPA 1',
            'kategori' => Divisi::KATEGORI_MEMBER,
            'color_cover' => '#00897b',
        ]);

        User::create([
            'id_divisi' => null,
            'instansi_id' => $instansiB->id,
            'tgl' => '2024-01-01',
            'nomor_induk' => 'ADM-B001',
            'username' => 'admin_sma1',
            'password' => Hash::make('password123'),
            'nama' => 'Admin SMA 1 Jakarta',
            'level' => 'Admin Instansi',
            'jk' => 'L'
        ]);

        User::create([
            'id_divisi' => $divisiB->id,
            'instansi_id' => $instansiB->id,
            'tgl' => '2024-01-01',
            'nomor_induk' => 'NIS-B001',
            'username' => 'siswa_sma1',
            'password' => Hash::make('password123'),
            'nama' => 'Siswa SMA 1 Jakarta',
            'level' => 'Member',
            'jk' => 'L'
        ]);

        // 6. Maintenance Mode & Software App
        MaintenanceMode::create([
            'is_mobile_maintenance' => false,
            'is_web_maintenance' => false,
        ]);

        SoftwareApp::create([
            'name' => 'Aku Sehat | V.1.0.0',
            'version' => '1.0.0',
            'description' => 'Initial release.',
            'link' => 'https://example.com/download',
            'status' => 'active'
        ]);

        $this->call(LeaderboardMultiSchoolSeeder::class);

        $this->command->info('Database seeded successfully with UUID and Multi-Tenant Isolation!');
        $this->command->info('Sekolah A: SMK Telesandi Bekasi (admin, admininstansi, gurubudi, kepsek, member1-50)');
        $this->command->info('Sekolah B: SMA 1 Jakarta (admin_sma1, siswa_sma1)');
        $this->command->info('SuperAdmin: superadmin');
    }
}