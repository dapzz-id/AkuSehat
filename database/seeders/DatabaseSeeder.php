<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Kelas
        $kelas = [
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'X TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'X TKJ 2', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'XI TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'XI TKJ 2', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'XII TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'X RPL 1', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'X RPL 2', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
            ['sekolah_id' => 1, 'tgl' => '2024-01-01', 'kelas' => 'XI RPL 1', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
        ];

        \App\Models\Sekolah::create([
            'npsn' => '20253675',
            'nama_sekolah' => 'SMK TELEKOMUNIKASI TELESANDI BEKASI',
            'alamat' => 'Jl. KH. Mochamad, Mekarsari, Kecamatan Tambun Selatan, Kabupaten Bekasi',
            'kota' => 'Bekasi',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '17510'
        ]);

        foreach ($kelas as $k) {
            Kelas::create($k);
        }

        // Create Users
        $users = [
            [
                'id_kelas' => null,
                'sekolah_id' => 1,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'nama' => 'Admin',
                'level' => 'Admin',
                'jk' => 'L'
            ],
            [
                'id_kelas' => null,
                'sekolah_id' => 1,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'healthmonitor',
                'password' => Hash::make('password123'),
                'nama' => 'Health Monitor',
                'level' => 'Health Monitor',
                'jk' => 'P'
            ],
            [
                'id_kelas' => null,
                'sekolah_id' => 1,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'healthconsultant',
                'password' => Hash::make('password123'),
                'nama' => 'Health Consultant',
                'level' => 'Health Consultant',
                'jk' => 'L'
            ],
            [
                'id_kelas' => null,
                'sekolah_id' => null,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'nama' => 'Super Admin',
                'level' => 'SuperAdmin',
                'jk' => 'L'
            ],
            [
                'id_kelas' => null,
                'sekolah_id' => 1,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'adminsekolah',
                'password' => Hash::make('password123'),
                'nama' => 'Admin Sekolah',
                'level' => 'Admin Sekolah',
                'jk' => 'P'
            ]
        ];

        \App\Models\MaintenanceMode::create([
            'is_mobile_maintenance' => 0
        ]);

        \App\Models\MaintenanceMode::create([
            'is_web_maintenance' => 0
        ]);

        \App\Models\SoftwareApp::create([
            'name' => 'Aku Sehat | V.1.0.0',
            'version' => '1.0.0',
            'description' => 'Initial release.',
            'link' => 'https://example.com/download',
            'status' => 'active'
        ]);

        \App\Models\LicenseKey::create([
            'key' => 'FREE-TRIAL-2024',
            'kuota_pengguna' => 800,
            'status' => \App\Models\LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addDays(15)
        ]);

        // Create sample students
        $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko'];
        $lastNames = ['Pratama', 'Sari', 'Putra', 'Putri', 'Wijaya', 'Kusuma', 'Handoko', 'Lestari'];
        
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $gender = ($i % 2 == 0) ? 'P' : 'L';
            $kelasId = rand(1, 8);
            
            $users[] = [
                'id_kelas' => $kelasId,
                'sekolah_id' => 1,
                'license_key_id' => 1,
                'tgl' => '2024-01-01',
                'nis' => str_pad($i, 6, '0', STR_PAD_LEFT),
                'username' => 'siswa' . $i,
                'password' => Hash::make('password123'),
                'nama' => $firstName . ' ' . $lastName,
                'jk' => $gender
            ];
        }

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('1. Admin Sekolah - username: adminsekolah, password: password123');
        $this->command->info('2. Admin - username: admin, password: password123');
        $this->command->info('3. Health Monitor - username: healthmonitor, password: password123');
        $this->command->info('4. Health Consultant - username: healthconsultant, password: password123');
        $this->command->info('5. Sample Students - username: siswa1-siswa50, password: password123');
    }
}
