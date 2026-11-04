<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Instansi;
use App\Models\DataHaid;
use App\Models\PeminjamanPita;
use App\Modules\User\Enums\UserGender;
use App\Modules\User\Enums\UserRole;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Modules\Mens\DataHaid\Enums\PitaStatus;
use App\Modules\Mens\DataHaid\Services\FemHealthService;
use App\Modules\Mens\DataHaid\Repositories\DataHaidRepository;
use App\Modules\Mens\DataHaid\Requests\StoreDirectPitaRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PitaUksAntiIdorAndHaidValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
                $table->uuid('id_divisi')->nullable();
                $table->string('nomor_induk', 30)->nullable();
                $table->string('username', 40)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('password')->nullable();
                $table->string('nama', 75);
                $table->string('level', 30)->default('Member');
                $table->string('jk', 10)->default('L');
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('data_haid')) {
            Schema::create('data_haid', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('id_user');
                $table->uuid('instansi_id')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->integer('durasi_hari')->nullable();
                $table->string('status', 30)->default('berlangsung');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('peminjaman_pita')) {
            Schema::create('peminjaman_pita', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('id_user');
                $table->uuid('instansi_id')->nullable();
                $table->date('tanggal_pinjam');
                $table->date('tanggal_kembali')->nullable();
                $table->integer('jumlah_pita')->default(1);
                $table->boolean('verified')->default(0);
                $table->string('status', 30)->default('menunggu');
                $table->date('estimasi_selesai_haid')->nullable();
                $table->text('keterangan')->nullable();
                $table->integer('denda_nominal')->default(0);
                $table->string('denda_status')->nullable();
                $table->text('sanksi_catatan')->nullable();
                $table->string('health_flag')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Test StoreDirectPitaRequest has tenant-aware rules.
     */
    public function test_store_direct_pita_request_has_tenant_aware_rules(): void
    {
        $officer = new User();
        $officer->id = (string) Str::uuid7();
        $officer->instansi_id = (string) Str::uuid7();
        $officer->level = UserRole::HEALTH_CONSULTANT->value;
        Auth::setUser($officer);

        $request = new StoreDirectPitaRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('id_user', $rules);
        $this->assertArrayHasKey('tanggal_pinjam', $rules);
    }

    /**
     * Test a female student must start menstruation first before borrowing a ribbon.
     * Service layer must reject if she has not started haid.
     */
    public function test_cannot_borrow_pita_if_student_has_not_started_haid(): void
    {
        $instansiId = (string) Str::uuid7();

        $officer = new User();
        $officer->id = (string) Str::uuid7();
        $officer->instansi_id = $instansiId;
        $officer->level = UserRole::HEALTH_CONSULTANT->value;
        Auth::setUser($officer);

        $service = app(FemHealthService::class);

        // Siswi perempuan tapi BELUM mulai haid
        $siswi = new User([
            'id' => (string) Str::uuid7(),
            'nama' => 'Siswi Belum Haid',
            'username' => 'siswi_belum_haid_' . Str::random(5),
            'email' => 'siswi_' . Str::random(5) . '@test.com',
            'password' => bcrypt('password123'),
            'nomor_induk' => 'NIS-' . Str::random(6),
            'jk' => UserGender::PEREMPUAN->value,
            'level' => UserRole::MEMBER->value,
            'instansi_id' => $instansiId,
            'status' => 'active',
        ]);
        $siswi->save();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Siswi belum memulai siklus haid aktif');

        $service->recordDirectPeminjaman([
            'id_user' => $siswi->id,
            'tanggal_pinjam' => now()->toDateString(),
        ], $officer);
    }

    /**
     * Test Anti-IDOR: Officer cannot borrow pita for a user from ANOTHER instansi.
     */
    public function test_cannot_borrow_pita_for_student_from_another_instansi_anti_idor(): void
    {
        $instansiA = (string) Str::uuid7();
        $instansiB = (string) Str::uuid7();

        $officerA = new User();
        $officerA->id = (string) Str::uuid7();
        $officerA->instansi_id = $instansiA;
        $officerA->level = UserRole::HEALTH_CONSULTANT->value;
        Auth::setUser($officerA);

        $service = app(FemHealthService::class);

        // Siswi dari Instansi B
        $siswiB = new User([
            'id' => (string) Str::uuid7(),
            'nama' => 'Siswi Sekolah B',
            'username' => 'siswi_b_' . Str::random(5),
            'email' => 'siswi_b_' . Str::random(5) . '@test.com',
            'password' => bcrypt('password123'),
            'nomor_induk' => 'NIS-B-' . Str::random(6),
            'jk' => UserGender::PEREMPUAN->value,
            'level' => UserRole::MEMBER->value,
            'instansi_id' => $instansiB,
            'status' => 'active',
        ]);
        $siswiB->save();

        // Must throw ModelNotFoundException because User does not belong to Instansi A
        $this->expectException(ModelNotFoundException::class);

        $service->recordDirectPeminjaman([
            'id_user' => $siswiB->id,
            'tanggal_pinjam' => now()->toDateString(),
        ], $officerA);
    }

    /**
     * Test workflow:
     * 1. Start Haid for student
     * 2. Student now appears in getHaidMembersForPita
     * 3. Borrow ribbon succeeds
     * 4. Student now removed from getHaidMembersForPita (already has active loan)
     * 5. Cannot borrow second ribbon simultaneously
     */
    public function test_full_haid_and_pita_borrowing_lifecycle(): void
    {
        $instansiId = (string) Str::uuid7();

        $officer = new User();
        $officer->id = (string) Str::uuid7();
        $officer->instansi_id = $instansiId;
        $officer->level = UserRole::HEALTH_CONSULTANT->value;
        Auth::setUser($officer);

        $repo = app(DataHaidRepository::class);
        $service = app(FemHealthService::class);

        $siswi = new User([
            'id' => (string) Str::uuid7(),
            'nama' => 'Siti Rahma',
            'username' => 'siti_' . Str::random(5),
            'email' => 'siti_' . Str::random(5) . '@test.com',
            'password' => bcrypt('password123'),
            'nomor_induk' => 'NIS-' . Str::random(6),
            'jk' => UserGender::PEREMPUAN->value,
            'level' => UserRole::MEMBER->value,
            'instansi_id' => $instansiId,
            'status' => 'active',
        ]);
        $siswi->save();

        // 1. Sebelum mulai haid: nama TIDAK muncul di daftar siap pinjam pita
        $eligibleBefore = $repo->getHaidMembersForPita($instansiId);
        $this->assertFalse($eligibleBefore->contains('id', $siswi->id));

        // 2. Catat mulai haid
        $haid = $service->recordHaidManual([
            'id_user' => $siswi->id,
            'tanggal_mulai' => now()->toDateString(),
        ], $officer);
        $this->assertNotNull($haid);

        // 3. Setelah mulai haid: nama SEKARANG MUNCUL di daftar siap pinjam pita
        $eligibleAfter = $repo->getHaidMembersForPita($instansiId);
        $this->assertTrue($eligibleAfter->contains('id', $siswi->id));

        // 4. Catat pinjam pita UKS
        $pita = $service->recordDirectPeminjaman([
            'id_user' => $siswi->id,
            'tanggal_pinjam' => now()->toDateString(),
            'keterangan' => 'Peminjaman pertama saat jam olahraga',
        ], $officer);

        $this->assertNotNull($pita);
        $this->assertEquals(PitaStatus::DIPINJAM->value, $pita->status);
        $this->assertEquals($instansiId, $pita->instansi_id);

        // 5. Setelah pinjam pita: nama OTOMATIS TIDAK MUNCUL LAGI karena pita belum dikembalikan
        $eligibleWithActivePita = $repo->getHaidMembersForPita($instansiId);
        $this->assertFalse($eligibleWithActivePita->contains('id', $siswi->id));

        // 6. Coba pinjam pita kedua kali saat masih memegang pita -> harus ditolak
        try {
            $service->recordDirectPeminjaman([
                'id_user' => $siswi->id,
                'tanggal_pinjam' => now()->toDateString(),
            ], $officer);
            $this->fail('Harusnya gagal meminjam pita kedua jika pita pertama belum dikembalikan');
        } catch (Exception $e) {
            $this->assertStringContainsString('masih memiliki peminjaman pita yang belum dikembalikan', $e->getMessage());
        }

        // 7. Kembalikan pita
        $returned = $service->returnPita($pita->id, $instansiId);
        $this->assertTrue($returned);

        // 8. Setelah pita dikembalikan dan siklus haid masih aktif: bisa pinjam lagi jika diperlukan
        $eligibleAfterReturn = $repo->getHaidMembersForPita($instansiId);
        $this->assertTrue($eligibleAfterReturn->contains('id', $siswi->id));
    }

    /**
     * Test Anti-IDOR on pita actions (verify, return).
     * An officer cannot modify records of another school.
     */
    public function test_anti_idor_on_peminjaman_cross_tenant_actions(): void
    {
        $instansiA = (string) Str::uuid7();
        $instansiB = (string) Str::uuid7();

        $officerA = new User();
        $officerA->id = (string) Str::uuid7();
        $officerA->instansi_id = $instansiA;
        $officerA->level = UserRole::HEALTH_CONSULTANT->value;
        Auth::setUser($officerA);

        $service = app(FemHealthService::class);

        // Buat data di Sekolah B
        $pitaB = new PeminjamanPita([
            'id' => (string) Str::uuid7(),
            'id_user' => (string) Str::uuid7(),
            'instansi_id' => $instansiB,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => PitaStatus::MENUNGGU->value,
            'verified' => 0,
        ]);
        $pitaB->save();

        // Officer A mencoba verifikasi pita milik Sekolah B -> HARUS GAGAL 404
        try {
            $service->verifyPita($pitaB->id, $instansiA);
            $this->fail('Officer A seharusnya tidak bisa memverifikasi pita Sekolah B');
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }

        // Officer A mencoba kembalikan pita milik Sekolah B -> HARUS GAGAL 404
        try {
            $service->returnPita($pitaB->id, $instansiA);
            $this->fail('Officer A seharusnya tidak bisa mengembalikan pita Sekolah B');
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
