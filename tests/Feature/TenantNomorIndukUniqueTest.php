<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Modules\User\Auth\Repositories\AuthRepository;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TenantNomorIndukUniqueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan skema SQLite in-memory dengan composite unique index per instansi
        if (!Schema::hasTable('instansi')) {
            Schema::create('instansi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_instansi')->nullable();
                $table->string('tipe_instansi')->nullable();
                $table->string('invite_code')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
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

    public function test_nomor_induk_can_be_same_across_different_instansi(): void
    {
        $instansiA = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMA Harapan Bangsa',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'HB' . rand(1000, 9999),
        ]);

        $instansiB = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMA Tunas Indonesia',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'TI' . rand(1000, 9999),
        ]);

        $sharedNomorInduk = 'NIS-99901';

        // User di Instansi A
        $userA = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansiA->id,
            'nomor_induk' => $sharedNomorInduk,
            'username' => 'siswa_a_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'Siswa A',
            'level' => 'Member',
            'jk' => 'L',
            'tgl' => '2026-01-01',
        ]);

        // User di Instansi B dengan nomor_induk yang sama (HARUS BERHASIL KARENA BEDA INSTANSI)
        $userB = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansiB->id,
            'nomor_induk' => $sharedNomorInduk,
            'username' => 'siswa_b_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'Siswa B',
            'level' => 'Member',
            'jk' => 'P',
            'tgl' => '2026-01-01',
        ]);

        $this->assertEquals($userA->nomor_induk, $userB->nomor_induk);
        $this->assertNotEquals($userA->instansi_id, $userB->instansi_id);
    }

    public function test_nomor_induk_cannot_be_duplicated_in_same_instansi(): void
    {
        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMK Negeri 1',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'SMK' . rand(1000, 9999),
        ]);

        $duplicateNomorInduk = 'NIS-SMK-01';

        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => $duplicateNomorInduk,
            'username' => 'user_pertama_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'User Pertama',
            'level' => 'Member',
            'jk' => 'L',
            'tgl' => '2026-01-01',
        ]);

        $this->expectException(QueryException::class);

        // Menambahkan nomor_induk yang sama di instansi yang sama (HARUS DITOLAK QUERYEXCEPTION)
        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nomor_induk' => $duplicateNomorInduk,
            'username' => 'user_kedua_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'User Kedua',
            'level' => 'Member',
            'jk' => 'P',
            'tgl' => '2026-01-01',
        ]);
    }

    public function test_username_is_strictly_unique_globally(): void
    {
        $globalUsername = 'uniqueuser' . rand(100, 999);

        User::create([
            'id' => (string) Str::uuid(),
            'username' => $globalUsername,
            'password' => bcrypt('password123'),
            'nama' => 'User Global 1',
            'level' => 'Member',
            'jk' => 'L',
            'tgl' => '2026-01-01',
        ]);

        $this->expectException(QueryException::class);

        // User lain mencoba menggunakan username yang sama (HARUS DITOLAK GLOBAL UNIQUE)
        User::create([
            'id' => (string) Str::uuid(),
            'username' => $globalUsername,
            'password' => bcrypt('password123'),
            'nama' => 'User Global 2',
            'level' => 'Member',
            'jk' => 'P',
            'tgl' => '2026-01-01',
        ]);
    }

    public function test_auth_repository_prevents_idor_when_nomor_induk_is_ambiguous(): void
    {
        $repo = new AuthRepository();

        $instansiX = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'Sekolah X',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'X' . rand(1000, 9999),
        ]);

        $instansiY = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'Sekolah Y',
            'tipe_instansi' => 'sekolah',
            'invite_code' => 'Y' . rand(1000, 9999),
        ]);

        $collisionNomorInduk = 'COLLISION-NIS-' . rand(1000, 9999);

        $userX = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansiX->id,
            'nomor_induk' => $collisionNomorInduk,
            'username' => 'siswa_x_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'Siswa X',
            'level' => 'Member',
            'jk' => 'L',
            'tgl' => '2026-01-01',
        ]);

        $userY = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansiY->id,
            'nomor_induk' => $collisionNomorInduk,
            'username' => 'siswa_y_' . rand(100, 999),
            'password' => bcrypt('password123'),
            'nama' => 'Siswa Y',
            'level' => 'Member',
            'jk' => 'P',
            'tgl' => '2026-01-01',
        ]);

        // Login dengan nomor induk yang ambigu (terdapat di 2 instansi)
        // Harus mengembalikan NULL demi mencegah IDOR dan kebocoran akun!
        $resolved = $repo->findByLogin($collisionNomorInduk);
        $this->assertNull($resolved, 'Harus null demi mencegah login IDOR acak saat nomor induk ada di beberapa instansi.');

        // Tetapi login dengan username unik global masing-masing HARUS berhasil
        $this->assertNotNull($repo->findByLogin($userX->username));
        $this->assertEquals($userX->id, $repo->findByLogin($userX->username)->id);

        $this->assertNotNull($repo->findByLogin($userY->username));
        $this->assertEquals($userY->id, $repo->findByLogin($userY->username)->id);
    }
}
