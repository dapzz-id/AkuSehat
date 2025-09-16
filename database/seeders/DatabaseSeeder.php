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
            ['tgl' => '2024-01-01', 'kelas' => 'X TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'X TKJ 2', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'XI TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'XI TKJ 2', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'XII TKJ 1', 'jurusan' => 'TKJ', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'X RPL 1', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'X RPL 2', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
            ['tgl' => '2024-01-01', 'kelas' => 'XI RPL 1', 'jurusan' => 'RPL', 'count_kesehatan' => 0],
        ];

        foreach ($kelas as $k) {
            Kelas::create($k);
        }

        // Create Users
        $users = [
            [
                'id_kelas' => null,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'adminpmr',
                'password' => Hash::make('password123'),
                'nama' => 'Admin PMR',
                'level' => 'Admin PMR',
                'jk' => 'L'
            ],
            [
                'id_kelas' => null,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'gurubk',
                'password' => Hash::make('password123'),
                'nama' => 'Guru BK',
                'level' => 'Guru BK',
                'jk' => 'P'
            ],
            [
                'id_kelas' => null,
                'tgl' => '2024-01-01',
                'nis' => null,
                'username' => 'guriolahraga',
                'password' => Hash::make('password123'),
                'nama' => 'Guru Olahraga',
                'level' => 'Guru Olahraga',
                'jk' => 'L'
            ],
        ];

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
                'tgl' => '2024-01-01',
                'nis' => str_pad($i, 6, '0', STR_PAD_LEFT),
                'username' => 'siswa' . $i,
                'password' => Hash::make('password123'),
                'nama' => $firstName . ' ' . $lastName,
                'level' => 'Siswa',
                'jk' => $gender
            ];
        }

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin PMR - username: adminpmr, password: password123');
        $this->command->info('Guru BK - username: gurubk, password: password123');
        $this->command->info('Guru Olahraga - username: guriolahraga, password: password123');
        $this->command->info('Sample Students - username: siswa1-siswa50, password: password123');
    }
}
