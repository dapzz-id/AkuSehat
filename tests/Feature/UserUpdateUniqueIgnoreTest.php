<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UserUpdateUniqueIgnoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('license_keys')) {
            Schema::create('license_keys', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('license_key')->unique();
                $table->uuid('instansi_id')->nullable()->index();
                $table->string('status')->default('active');
                $table->date('tanggal_berakhir')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('instansi')) {
            Schema::create('instansi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_instansi')->nullable();
                $table->string('tipe_instansi')->nullable();
                $table->string('invite_code')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('divisi')) {
            Schema::create('divisi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->index();
                $table->string('tgl', 20)->default('2026-01-01');
                $table->string('divisi_name', 100);
                $table->string('color_cover')->default('#2e7d32');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('id_divisi')->nullable();
                $table->string('nomor_induk', 30)->nullable();
                $table->string('username', 20)->unique();
                $table->string('password');
                $table->string('nama', 75);
                $table->string('level', 30)->default('Member');
                $table->string('jk', 10)->default('L');
                $table->string('tgl', 20)->nullable();
                $table->timestamps();

                $table->unique(['instansi_id', 'nomor_induk'], 'users_instansi_nomor_induk_unique');
            });
        }
    }

    public function test_updating_own_user_with_same_nomor_induk_and_username_passes_validation(): void
    {
        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMKS Telesandi',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'TLS' . rand(100, 999),
        ]);

        $divisi = Divisi::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'divisi_name' => 'Guru',
        ]);

        $admin = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisi->id,
            'nomor_induk' => 'ADM-001',
            'username' => 'admintels',
            'password' => bcrypt('password123'),
            'nama' => 'Admin Sekolah',
            'level' => 'Admin Instansi',
            'jk' => 'L',
        ]);

        $targetUser = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisi->id,
            'nomor_induk' => 'PMR-001',
            'username' => 'adminpmrtels',
            'password' => bcrypt('password123'),
            'nama' => 'PMR Lama',
            'level' => 'Admin PMR',
            'jk' => 'L',
        ]);

        \Illuminate\Support\Facades\DB::table('license_keys')->insert([
            'id' => (string) Str::uuid(),
            'license_key' => 'AKU-TEST-KEY',
            'instansi_id' => $instansi->id,
            'status' => 'active',
            'tanggal_berakhir' => now()->addYear()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simulasikan Admin Instansi mengupdate targetUser tanpa mengubah nomor_induk dan username
        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $targetUser->id), [
                'nomor_induk' => 'PMR-001',
                'username' => 'adminpmrtels',
                'nama' => 'PMR Baru Diperbarui',
                'jk' => 'L',
                'id_divisi' => $divisi->id,
                'level' => 'Admin PMR',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'nama' => 'PMR Baru Diperbarui',
            'nomor_induk' => 'PMR-001',
            'username' => 'adminpmrtels',
        ]);
    }

    public function test_updating_user_to_another_users_nomor_induk_fails_validation(): void
    {
        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMKS Telesandi 2',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'TLS2' . rand(100, 999),
        ]);

        $admin = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'ADM-002',
            'username' => 'admintels2',
            'password' => bcrypt('password123'),
            'nama' => 'Admin 2',
            'level' => 'Admin Instansi',
            'jk' => 'L',
        ]);

        $user1 = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'PMR-001',
            'username' => 'pmr001',
            'password' => bcrypt('password123'),
            'nama' => 'User Satu',
            'level' => 'Admin PMR',
            'jk' => 'L',
        ]);

        $user2 = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'PMR-002',
            'username' => 'pmr002',
            'password' => bcrypt('password123'),
            'nama' => 'User Dua',
            'level' => 'Admin PMR',
            'jk' => 'L',
        ]);

        // Coba ubah nomor_induk user2 menjadi nomor_induk user1 (harus gagal validasi)
        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $user2->id), [
                'nomor_induk' => 'PMR-001',
                'username' => 'pmr002',
                'nama' => 'User Dua Ganti',
                'jk' => 'L',
                'level' => 'Admin PMR',
            ]);

        $response->assertSessionHasErrors(['nomor_induk']);
    }

    public function test_updating_user_to_another_users_username_fails_validation(): void
    {
        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMKS Telesandi 3',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'TLS3' . rand(100, 999),
        ]);

        $admin = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'ADM-003',
            'username' => 'admintels3',
            'password' => bcrypt('password123'),
            'nama' => 'Admin 3',
            'level' => 'Admin Instansi',
            'jk' => 'L',
        ]);

        $user1 = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'PMR-010',
            'username' => 'pmr010',
            'password' => bcrypt('password123'),
            'nama' => 'User Sepuluh',
            'level' => 'Admin PMR',
            'jk' => 'L',
        ]);

        $user2 = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => 'PMR-020',
            'username' => 'pmr020',
            'password' => bcrypt('password123'),
            'nama' => 'User Dua Puluh',
            'level' => 'Admin PMR',
            'jk' => 'L',
        ]);

        // Coba ubah username user2 menjadi username user1 (harus gagal validasi)
        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $user2->id), [
                'nomor_induk' => 'PMR-020',
                'username' => 'pmr010',
                'nama' => 'User Dua Puluh Ganti',
                'jk' => 'L',
                'level' => 'Admin PMR',
            ]);

        $response->assertSessionHasErrors(['username']);
    }
}
