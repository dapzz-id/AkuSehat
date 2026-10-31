<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MaintenanceMode;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Kredensial Akun SuperAdmin untuk Lingkungan Production.
     * Didefinisikan langsung di dalam seeder (bukan di file .env) untuk mencegah
     * risiko kebocoran data jika file .env server tidak sengaja terekspos.
     * Anda dapat mengubah nilai konstanta ini langsung sebelum menjalankan seeder.
     */
    public const SUPERADMIN_NAME        = 'Neura Cakrawira Solusi | Aku Sehat';
    public const SUPERADMIN_USERNAME    = 'neuracakrawirasolusi';
    public const SUPERADMIN_EMAIL       = 'superadmin@akusehat.neuracakrawira.asia';
    
    /**
     * Opsi 1 (Plaintext): Akan di-hash otomatis menggunakan Bcrypt salted hashing (12 rounds).
     * Opsi 2 (Salted Hash): Isi konstanta SUPERADMIN_PASSWORD_HASH dengan hash Bcrypt ($2y$12$...)
     * sehingga plaintext password sama sekali TIDAK TERTULIS di file / repositori Git!
     */
    public const SUPERADMIN_PASSWORD      = 'SuperAdminSecure2026!';
    public const SUPERADMIN_PASSWORD_HASH = null;
    public const SUPERADMIN_NOMOR_INDUK   = 'SA-NCS-001';

    /**
     * Run the database seeds for PRODUCTION environment.
     * Hanya membuat akun SuperAdmin dan row default MaintenanceMode (tanpa data testing/dummy).
     */
    public function run(): void
    {
        $this->command->info('=====================================================');
        $this->command->info('🚀 MENJALANKAN DATABASE SEEDER UNTUK PRODUCTION');
        $this->command->info('=====================================================');

        $username   = self::SUPERADMIN_USERNAME;
        $email      = self::SUPERADMIN_EMAIL;
        $name       = self::SUPERADMIN_NAME;
        $nomorInduk = self::SUPERADMIN_NOMOR_INDUK;

        // Tentukan password hash: gunakan pre-computed salted hash atau hash plaintext dengan Bcrypt
        $hashedPassword = !empty(self::SUPERADMIN_PASSWORD_HASH)
            ? self::SUPERADMIN_PASSWORD_HASH
            : Hash::make(self::SUPERADMIN_PASSWORD);

        // 1. Cek apakah akun SuperAdmin sudah ada di database (Idempotent check)
        $existingSuperAdmin = User::withoutGlobalScopes()
            ->where('level', UserRole::SUPERADMIN->value)
            ->orWhere('username', $username)
            ->orWhere('email', $email)
            ->first();

        if ($existingSuperAdmin) {
            $this->command->warn("⚠️  Akun SuperAdmin sudah terdaftar sebelumnya:");
            $this->command->line("   - ID       : {$existingSuperAdmin->id}");
            $this->command->line("   - Username : {$existingSuperAdmin->username}");
            $this->command->line("   - Email    : {$existingSuperAdmin->email}");
            $this->command->line("   - Role     : {$existingSuperAdmin->level}");
            $this->command->line("   - Status   : {$existingSuperAdmin->status}");
            $this->command->comment("ℹ️  Kredensial tidak ditimpa ulang (aman untuk re-run).");
        } else {
            // 2. Buat akun SuperAdmin pertama untuk produksi langsung dengan kredensial seeder
            $superAdmin = User::create([
                'instansi_id'   => null, // SuperAdmin berdiri sendiri di level sistem, tidak terikat instansi manapun
                'id_divisi'     => null,
                'tgl'           => now()->toDateString(),
                'nomor_induk'   => $nomorInduk,
                'username'      => $username,
                'email'         => $email,
                'password'      => $hashedPassword,
                'nama'          => $name,
                'level'         => UserRole::SUPERADMIN->value,
                'status'        => UserStatus::AKTIF->value,
                'jk'            => 'L',
            ]);

            $this->command->info("✅ Akun SuperAdmin berhasil dibuat menggunakan Bcrypt Salted Hashing!");
            $this->command->table(
                ['Field', 'Nilai'],
                [
                    ['Nama', $superAdmin->nama],
                    ['Username', $superAdmin->username],
                    ['Email', $superAdmin->email],
                    ['Role', $superAdmin->level],
                    ['Status', $superAdmin->status],
                    ['Nomor Induk', $superAdmin->nomor_induk],
                ]
            );
        }

        // 3. Inisialisasi row default MaintenanceMode (false) agar sistem stabil tanpa data dummy
        MaintenanceMode::firstOrCreate([], [
            'is_web_maintenance'    => false,
            'is_mobile_maintenance' => false,
        ]);

        $this->command->info('=====================================================');
        $this->command->info('✨ Production Seeding selesai (100% tanpa data dummy)!');
        $this->command->info('=====================================================');
    }
}
