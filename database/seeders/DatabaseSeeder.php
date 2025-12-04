<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\LicenseKey::create([
            'key' => 'FREE-TRIAL-2024',
            'kuota_pengguna' => 800,
            'status' => \App\Models\LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addDays(15)
        ]);
        
        // Create Divisi
        $divisi = [
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'X TKJ 1'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'X TKJ 2'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'XI TKJ 1'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'XI TKJ 2'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'XII TKJ 1'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'X RPL 1'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'X RPL 2'],
            ['instansi_id' => 1, 'tgl' => '2024-01-01', 'divisi_name' => 'XI RPL 1'],
        ];

        \App\Models\Instansi::create([
            'nama_instansi' => 'SMK TELEKOMUNIKASI TELESANDI BEKASI',
            'alamat' => 'Jl. KH. Mochamad, Mekarsari, Kecamatan Tambun Selatan, Kabupaten Bekasi',
            'kota' => 'Bekasi',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '17510',
            'license_id_active' => 1,
        ]);

        foreach ($divisi as $k) {
            Divisi::create($k);
        }

        // Create Users
        $users = [
            [
                'id_divisi' => null,
                'license_key_id' => 1,
                'instansi_id' => 1,
                'tgl' => '2024-01-01',
                'nomor_induk' => null,
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'nama' => 'Admin',
                'level' => 'Admin',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'license_key_id' => 1,
                'instansi_id' => 1,
                'tgl' => '2024-01-01',
                'nomor_induk' => null,
                'username' => 'healthmonitor',
                'password' => Hash::make('password123'),
                'nama' => 'Health Monitor',
                'level' => 'Health Monitor',
                'jk' => 'P'
            ],
            [
                'id_divisi' => null,
                'license_key_id' => 1,
                'instansi_id' => 1,
                'tgl' => '2024-01-01',
                'nomor_induk' => null,
                'username' => 'healthconsultant',
                'password' => Hash::make('password123'),
                'nama' => 'Health Consultant',
                'level' => 'Health Consultant',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => null,
                'tgl' => '2024-01-01',
                'nomor_induk' => null,
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'nama' => 'Super Admin',
                'level' => 'SuperAdmin',
                'jk' => 'L'
            ],
            [
                'id_divisi' => null,
                'instansi_id' => 1,
                'tgl' => '2024-01-01',
                'nomor_induk' => null,
                'username' => 'admininstansi',
                'password' => Hash::make('password123'),
                'nama' => 'Admin Instansi',
                'level' => 'Admin Instansi',
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

        // Create sample students
        $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko'];
        $lastNames = ['Pratama', 'Sari', 'Putra', 'Putri', 'Wijaya', 'Kusuma', 'Handoko', 'Lestari'];
        
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $gender = ($i % 2 == 0) ? 'P' : 'L';
            $divisiId = rand(1, 8);
            
            $users[] = [
                'id_divisi' => $divisiId,
                'instansi_id' => 1,
                'license_key_id' => 1,
                'tgl' => '2024-01-01',
                'nomor_induk' => str_pad($i, 6, '0', STR_PAD_LEFT),
                'username' => 'member' . $i,
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
        $this->command->info('1. Admin Instansi - username: admininstansi, password: password123');
        $this->command->info('2. Admin - username: admin, password: password123');
        $this->command->info('3. Health Monitor - username: healthmonitor, password: password123');
        $this->command->info('4. Health Consultant - username: healthconsultant, password: password123');
        $this->command->info('5. Sample Students - username: siswa1-siswa50, password: password123');
    }
}