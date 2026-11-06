<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;
use App\Models\Kesehatan;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Enums\UserGender;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudPermissionsTest extends TestCase
{
    protected Instansi $instansi;
    protected Divisi $divisi;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('instansi')) {
            Schema::create('instansi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_instansi')->nullable();
                $table->string('jenis_instansi')->default('sekolah');
                $table->string('email')->nullable();
                $table->string('invite_code')->nullable();
                $table->string('status')->default('aktif');
                $table->json('active_modules')->nullable();
                $table->json('module_permissions')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('divisi')) {
            Schema::create('divisi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->string('divisi_name');
                $table->string('kategori')->default('member');
                $table->string('color_cover')->default('green');
                $table->string('description', 255)->nullable();
                $table->json('active_modules')->nullable();
                $table->json('module_permissions')->nullable();
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
                $table->string('status', 20)->default('aktif');
                $table->json('custom_modules')->nullable();
                $table->json('custom_permissions')->nullable();
                $table->boolean('is_tester')->default(false);
                $table->dateTime('suspended_until')->nullable();
                $table->string('ban_reason')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kesehatan')) {
            Schema::create('kesehatan', function (Blueprint $table) {
                $table->uuid('id_kesehatan')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('id_user');
                $table->date('tgl');
                $table->decimal('bb', 5, 2)->nullable();
                $table->decimal('tb', 5, 2)->nullable();
                $table->integer('sistol')->nullable();
                $table->integer('diastol')->nullable();
                $table->string('status_darah', 50)->nullable();
                $table->decimal('imt', 5, 2)->nullable();
                $table->string('status', 50)->nullable();
                $table->text('pesan_imt')->nullable();
                $table->text('pesan_tkd')->nullable();
                $table->string('kondisi_telinga', 50)->nullable();
                $table->string('kondisi_gigi', 50)->nullable();
                $table->string('perilaku_beresiko', 50)->nullable();
                $table->string('gangguan_reproduksi', 50)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hb')) {
            Schema::create('hb', function (Blueprint $table) {
                $table->uuid('id_hb')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('id_user');
                $table->uuid('id_divisi')->nullable();
                $table->date('tgl');
                $table->decimal('hb', 4, 1)->nullable();
                $table->string('status', 50)->nullable();
                $table->text('pesan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('user_id')->nullable();
                $table->uuid('instansi_id')->nullable();
                $table->string('action');
                $table->string('category')->default('system');
                $table->text('description');
                $table->json('context')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('global_module_configs')) {
            Schema::create('global_module_configs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('module_key')->unique();
                $table->string('name')->nullable();
                $table->string('status', 30)->default('active');
                $table->text('maintenance_message')->nullable();
                $table->json('tester_user_ids')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('counseling_sessions')) {
            Schema::create('counseling_sessions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('user_id')->nullable();
                $table->uuid('consultant_id')->nullable();
                $table->dateTime('session_date')->nullable();
                $table->string('topic', 150)->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('menunggu');
                $table->boolean('is_confidential')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('peminjaman_pita')) {
            Schema::create('peminjaman_pita', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('id_user')->nullable();
                $table->uuid('instansi_id')->nullable();
                $table->date('tanggal_pinjam')->nullable();
                $table->date('tanggal_kembali')->nullable();
                $table->integer('jumlah_pita')->default(1);
                $table->tinyInteger('verified')->default(0);
                $table->string('status')->default('dipinjam');
                $table->date('estimasi_selesai_haid')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_activities')) {
            Schema::create('user_activities', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable();
                $table->uuid('instansi_id')->nullable();
                $table->string('activity_type', 30)->default('run');
                $table->string('title')->nullable();
                $table->decimal('distance_meters', 10, 2)->default(0);
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->decimal('calories', 8, 2)->default(0);
                $table->string('avg_pace', 10)->nullable();
                $table->decimal('max_speed', 6, 2)->nullable();
                $table->unsignedInteger('steps')->default(0);
                $table->json('route_coordinates')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_vital_logs')) {
            Schema::create('user_vital_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable();
                $table->uuid('instansi_id')->nullable();
                $table->date('log_date')->nullable();
                $table->unsignedInteger('water_intake_ml')->default(0);
                $table->decimal('sleep_hours', 4, 1)->nullable();
                $table->tinyInteger('mood_score')->nullable();
                $table->unsignedSmallInteger('systolic')->nullable();
                $table->unsignedSmallInteger('diastolic')->nullable();
                $table->decimal('blood_sugar', 6, 2)->nullable();
                $table->decimal('weight_kg', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        $this->instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMK Telekomunikasi Telesandi',
            'jenis_instansi' => 'sekolah',
            'email' => 'admin@telesandi.sch.id',
            'status' => 'aktif',
        ]);

        $this->divisi = Divisi::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $this->instansi->id,
            'divisi_name' => 'Kelas X RPL 1',
            'kategori' => 'member',
        ]);
    }

    public function test_superadmin_and_admin_instansi_have_full_crud_permissions(): void
    {
        $superAdmin = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Super Administrator',
            'username' => 'superadmin_crud_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::SUPERADMIN->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $adminInstansi = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Kepala Sekolah',
            'username' => 'admin_instansi_crud_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $this->assertTrue($superAdmin->canCreate());
        $this->assertTrue($superAdmin->canRead());
        $this->assertTrue($superAdmin->canUpdate());
        $this->assertTrue($superAdmin->canDelete());
        $this->assertTrue($superAdmin->canAddUser());

        $this->assertTrue($adminInstansi->canCreate());
        $this->assertTrue($adminInstansi->canRead());
        $this->assertTrue($adminInstansi->canUpdate());
        $this->assertTrue($adminInstansi->canDelete());
        $this->assertTrue($adminInstansi->canAddUser());
    }

    public function test_divisi_crud_permissions_cascade_to_users(): void
    {
        $this->divisi->update([
            'module_permissions' => [
                'can_create' => true,
                'can_read' => true,
                'can_update' => false,
                'can_delete' => false,
                'can_add_user' => false,
            ],
        ]);

        $user = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Staf UKS Kelas',
            'username' => 'staf_uks_divisi_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::PEREMPUAN->value,
        ]);

        $this->assertTrue($user->canCreate());
        $this->assertTrue($user->canRead());
        $this->assertFalse($user->canUpdate());
        $this->assertFalse($user->canDelete());
        $this->assertFalse($user->canAddUser());
    }

    public function test_user_custom_permissions_override_divisi(): void
    {
        $this->divisi->update([
            'module_permissions' => [
                'can_create' => false,
                'can_read' => true,
                'can_update' => false,
                'can_delete' => false,
            ],
        ]);

        $user = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Staf Prioritas',
            'username' => 'staf_prioritas_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
            'custom_permissions' => [
                'can_create' => true,
                'can_update' => true,
                'can_delete' => true,
            ],
        ]);

        // User overrides divisi's restrictions
        $this->assertTrue($user->canCreate());
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canUpdate());
        $this->assertTrue($user->canDelete());
    }

    public function test_update_divisi_permissions_endpoint_persists_crud_checkboxes(): void
    {
        $adminInstansi = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Admin Instansi',
            'username' => 'admin_tester_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $response = $this->actingAs($adminInstansi)->post(route('admin.divisi.permissions', $this->divisi->id), [
            'use_custom_modules' => '1',
            'active_modules' => ['kesehatan', 'hemoglobin'],
            'permissions' => [
                'can_create' => '1',
                'can_read' => '1',
                'can_update' => '1',
                'can_delete' => '0',
                'can_add_user' => '1',
            ],
            'description' => 'Divisi Khusus Kelas Unggulan',
        ]);

        $response->assertRedirect(route('admin.divisi.index'));

        $this->divisi->refresh();
        $this->assertEquals('Divisi Khusus Kelas Unggulan', $this->divisi->description);
        $this->assertEquals(['kesehatan', 'hemoglobin'], $this->divisi->active_modules);
        $this->assertTrue($this->divisi->module_permissions['can_create']);
        $this->assertTrue($this->divisi->module_permissions['can_read']);
        $this->assertTrue($this->divisi->module_permissions['can_update']);
        $this->assertFalse($this->divisi->module_permissions['can_delete']);
        $this->assertTrue($this->divisi->module_permissions['can_add_user']);
        $this->assertIsArray($this->divisi->module_permissions['modules']);
    }

    public function test_update_user_permissions_endpoint_persists_crud_checkboxes(): void
    {
        $adminInstansi = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Admin Instansi',
            'username' => 'admin_tester2_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $targetUser = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Target User Test',
            'username' => 'target_user_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $response = $this->actingAs($adminInstansi)->post(route('admin.users.permissions', $targetUser->id), [
            'use_custom_modules' => '1',
            'custom_modules' => ['kesehatan', 'vaksinasi'],
            'custom_permissions' => [
                'can_create' => '1',
                'can_read' => '1',
                'can_update' => '1',
                'can_delete' => '1',
                'can_add_user' => '1',
            ],
        ]);

        $response->assertRedirect();

        $targetUser->refresh();
        $this->assertEquals(['kesehatan', 'vaksinasi'], $targetUser->custom_modules);
        $this->assertTrue($targetUser->custom_permissions['can_create']);
        $this->assertTrue($targetUser->custom_permissions['can_read']);
        $this->assertTrue($targetUser->custom_permissions['can_update']);
        $this->assertTrue($targetUser->custom_permissions['can_delete']);
        $this->assertTrue($targetUser->custom_permissions['can_add_user']);
        $this->assertIsArray($targetUser->custom_permissions['modules']);

        $this->assertTrue($targetUser->canDelete());
        $this->assertTrue($targetUser->canUpdate());
    }

    public function test_kesehatan_controller_enforces_crud_permissions(): void
    {
        // User with can_delete = false
        $petugas = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Petugas Medis Terbatas',
            'username' => 'petugas_terbatas_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
            'custom_permissions' => [
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => false,
            ],
        ]);

        $targetStudent = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Siswa Diperiksa',
            'username' => 'siswa_diperiksa_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $kesehatan = Kesehatan::create([
            'instansi_id' => $this->instansi->id,
            'id_user' => $targetStudent->id,
            'tgl' => '2026-11-05',
            'bb' => 55,
            'tb' => 165,
            'sistol' => 120,
            'diastol' => 80,
            'status_darah' => 'Normal',
            'imt' => 20.2,
            'status' => 'Normal',
        ]);

        // Attempt delete when can_delete is false -> should be blocked and redirected with error
        $response = $this->actingAs($petugas)->delete(route('admin.kesehatan.destroy', $kesehatan->id_kesehatan));
        $response->assertRedirect(route('admin.kesehatan.index'));
        $response->assertSessionHas('error');

        // Record still exists
        $this->assertDatabaseHas('kesehatan', ['id_kesehatan' => $kesehatan->id_kesehatan]);

        // Grant can_delete = true
        $petugas->update([
            'custom_permissions' => [
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => true,
            ],
        ]);

        $responseSuccess = $this->actingAs($petugas)->delete(route('admin.kesehatan.destroy', $kesehatan->id_kesehatan));
        $responseSuccess->assertRedirect(route('admin.kesehatan.index'));
        $responseSuccess->assertSessionHas('success');

        // Record now deleted
        $this->assertDatabaseMissing('kesehatan', ['id_kesehatan' => $kesehatan->id_kesehatan]);
    }

    public function test_health_consultant_can_access_divisi_and_users_with_can_read(): void
    {
        $consultant = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Guru BK Konselor',
            'username' => 'guru_bk_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_CONSULTANT->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::PEREMPUAN->value,
        ]);

        $this->assertTrue($consultant->canRead());

        // Can access /admin/users and /admin/divisi index
        $resUsers = $this->actingAs($consultant)->get(route('admin.users.index'));
        $resUsers->assertStatus(200);

        $resDivisi = $this->actingAs($consultant)->get(route('admin.divisi.index'));
        $resDivisi->assertStatus(200);

        // However, cannot modify permissions (restricted to Admin Instansi / Admin)
        $resPerm = $this->actingAs($consultant)->post(route('admin.divisi.permissions', $this->divisi->id), [
            'permissions' => ['can_delete' => '1'],
        ]);
        $resPerm->assertStatus(404);

        // If can_read is explicitly revoked, accessing returns 404
        $consultant->update([
            'custom_permissions' => [
                'can_read' => false,
            ],
        ]);
        $this->assertFalse($consultant->canRead());

        $this->actingAs($consultant)->get(route('admin.users.index'))->assertStatus(404);
        $this->actingAs($consultant)->get(route('admin.divisi.index'))->assertStatus(404);
    }

    public function test_health_consultant_read_only_on_kesehatan_hides_create_edit_delete_buttons(): void
    {
        $consultant = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Health Consultant Tels',
            'username' => 'hc_tels_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_CONSULTANT->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
            'custom_permissions' => [
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => false,
                'modules' => [
                    'kesehatan' => ['c' => false, 'r' => true, 'u' => false, 'd' => false],
                    'konsultasi' => ['c' => true, 'r' => true, 'u' => true, 'd' => false],
                ],
                'duty_modules' => [
                    'kesehatan' => ['c' => false, 'r' => true, 'u' => false, 'd' => false],
                    'konsultasi' => ['c' => true, 'r' => true, 'u' => true, 'd' => false],
                ],
            ],
        ]);

        $this->assertFalse($consultant->canCreate('kesehatan'));
        $this->assertFalse($consultant->canUpdate('kesehatan'));
        $this->assertFalse($consultant->canDelete('kesehatan'));
        $this->assertTrue($consultant->canRead('kesehatan'));

        $res = $this->actingAs($consultant)->get(route('admin.kesehatan.index'));
        $res->assertStatus(200);
        $res->assertSee('Mode Pemantauan & Read-Only', false);
        $res->assertDontSee('+ Tambah');
        $res->assertDontSee('Hapus Terpilih');
        $res->assertDontSee('Tambah Data Kesehatan');
    }

    public function test_member_cannot_submit_vitals_if_not_enabled_in_instansi(): void
    {
        $member = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Siswa Member',
            'username' => 'siswa_member_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // 1. Instansi has not enabled member_can_input_vitals
        $this->assertFalse($member->canInputVitals());

        // Calling vital-logs endpoint returns 403 Forbidden
        $responseAddWater = $this->actingAs($member, 'sanctum')->postJson('/api/vital-logs/water/add', [
            'amount' => 250,
        ]);
        $responseAddWater->assertStatus(403);
        $responseAddWater->assertJson([
            'success' => false,
            'message' => 'Penginputan tanda vital mandiri untuk member belum diaktifkan oleh institusi Anda.',
        ]);

        $responseUpdate = $this->actingAs($member, 'sanctum')->postJson('/api/vital-logs', [
            'log_date' => now()->format('Y-m-d'),
            'water_intake_ml' => 500,
        ]);
        $responseUpdate->assertStatus(403);

        // 2. Now Admin Instansi enables member_can_input_vitals
        $this->instansi->update([
            'module_permissions' => [
                'member_can_input_vitals' => true,
            ],
        ]);
        $member->refresh();
        $this->assertTrue($member->canInputVitals());

        $responseSuccess = $this->actingAs($member, 'sanctum')->postJson('/api/vital-logs/water/add', [
            'amount' => 250,
        ]);
        $responseSuccess->assertStatus(200);
        $responseSuccess->assertJson(['success' => true]);
    }

    public function test_health_monitor_cannot_add_or_import_users_when_not_checked_in_instansi(): void
    {
        $monitor = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Guru Penjas',
            'username' => 'guru_penjas_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // In default instansi (not checked)
        $this->assertFalse($monitor->canAddUser());

        // GET create form returns 404
        $resCreate = $this->actingAs($monitor)->get(route('admin.users.create'));
        $resCreate->assertStatus(404);

        // POST store user returns 404
        $resStore = $this->actingAs($monitor)->post(route('admin.users.store'), [
            'nama' => 'User Baru',
            'username' => 'newuser_' . Str::random(4),
            'password' => 'secret123',
            'nomor_induk' => '99999',
            'jk' => 'L',
            'level' => 'Member',
            'id_divisi' => $this->divisi->id,
        ]);
        $resStore->assertStatus(404);

        // Cannot create/clone or delete divisi
        $resStoreDivisi = $this->actingAs($monitor)->post(route('admin.divisi.store'), [
            'divisi' => 'Kelas Baru',
            'cover_color' => 'blue',
        ]);
        $resStoreDivisi->assertRedirect(route('admin.divisi.index'));
        $resStoreDivisi->assertSessionHas('error');
    }

    public function test_granular_per_module_crud_permissions_matrix(): void
    {
        // 1. Role defaults
        $consultant = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Guru BK Konselor',
            'username' => 'konselor_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_CONSULTANT->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::PEREMPUAN->value,
        ]);

        $this->assertTrue($consultant->canCreate('konsultasi'));
        $this->assertTrue($consultant->canRead('konsultasi'));
        $this->assertTrue($consultant->canUpdate('konsultasi'));
        $this->assertTrue($consultant->canRead('kesehatan'));
        $this->assertFalse($consultant->canCreate('kesehatan'));
        $this->assertFalse($consultant->canDelete('kesehatan'));

        $monitor = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Guru Penjaskes',
            'username' => 'penjas_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $this->assertTrue($monitor->canCreate('kesehatan'));
        $this->assertTrue($monitor->canCreate('hemoglobin'));
        $this->assertTrue($monitor->canRead('konsultasi'));
        $this->assertFalse($monitor->canCreate('konsultasi'));

        // 2. Admin updates division with granular per-module CRUD
        $adminInstansi = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Admin Instansi Granular',
            'username' => 'admin_gran_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $responseDiv = $this->actingAs($adminInstansi)->post(route('admin.divisi.permissions', $this->divisi->id), [
            'use_custom_modules' => '1',
            'permissions' => [
                'modules' => [
                    'olahraga' => ['c' => '1', 'r' => '1', 'u' => '1', 'd' => '0'],
                    'kesehatan' => ['c' => '0', 'r' => '1', 'u' => '0', 'd' => '0'],
                ],
                'can_add_user' => '0',
            ],
            'description' => 'Divisi Granular CRUD',
        ]);
        $responseDiv->assertRedirect(route('admin.divisi.index'));

        $this->divisi->refresh();
        $this->assertTrue($this->divisi->module_permissions['modules']['olahraga']['c']);
        $this->assertTrue($this->divisi->module_permissions['modules']['kesehatan']['r']);
        $this->assertFalse($this->divisi->module_permissions['modules']['kesehatan']['c']);

        // Member in this division inherits division's per-module permissions
        $member = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Siswa Divisi Granular',
            'username' => 'siswa_gran_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $this->assertTrue($member->canCreate('olahraga'));
        $this->assertTrue($member->canRead('kesehatan'));
        $this->assertFalse($member->canCreate('kesehatan'));

        // 3. User override via user permissions endpoint
        $responseUser = $this->actingAs($adminInstansi)->post(route('admin.users.permissions', $member->id), [
            'use_custom_modules' => '1',
            'custom_permissions' => [
                'modules' => [
                    'kesehatan' => ['c' => '1', 'r' => '1', 'u' => '1', 'd' => '0'],
                ],
                'can_add_user' => '1',
            ],
        ]);
        $responseUser->assertRedirect();

        $member->refresh();
        $this->assertTrue($member->canCreate('kesehatan'));
        $this->assertTrue($member->canAddUser());
    }

    public function test_custom_user_permissions_and_module_active_takes_top_priority_over_division_conflict(): void
    {
        $adminInstansi = User::create([
            'instansi_id' => $this->instansi->id,
            'nama' => 'Admin Instansi Conflict Test',
            'username' => 'admin_conflict_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // Atur Divisi:
        // - Modul 'haid_pita': NONAKTIF (tidak ada di active_modules divisi)
        // - Modul 'kesehatan': AKTIF dengan full CRUD (c=1, r=1, u=1, d=1)
        // - Modul 'olahraga': AKTIF dengan full CRUD (c=1, r=1, u=1, d=1)
        $this->divisi->update([
            'active_modules' => ['kesehatan', 'olahraga'],
            'module_permissions' => [
                'modules' => [
                    'kesehatan' => ['c' => true, 'r' => true, 'u' => true, 'd' => true],
                    'olahraga' => ['c' => true, 'r' => true, 'u' => true, 'd' => true],
                    'haid_pita' => ['c' => false, 'r' => false, 'u' => false, 'd' => false],
                ],
                'can_create' => true,
                'can_read' => true,
                'can_update' => true,
                'can_delete' => true,
            ],
        ]);

        // Buat user dalam divisi ini
        $user = User::create([
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'nama' => 'Pengguna Uji Konflik',
            'username' => 'user_conflict_' . Str::random(5),
            'password' => bcrypt('password123'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // Sebelum kustomisasi: user mengikuti divisi
        $this->assertFalse($user->isModuleActive('haid_pita'));
        $this->assertFalse($user->canCreate('haid_pita'));
        $this->assertTrue($user->isModuleActive('kesehatan'));
        $this->assertTrue($user->canDelete('kesehatan'));
        $this->assertTrue($user->isModuleActive('olahraga'));
        $this->assertTrue($user->canCreate('olahraga'));

        // =========================================================================
        // SEKARANG ADMIN MELAKUKAN KUSTOMISASI KHUSUS PADA AKUN USER INI:
        // 1. Nabrak 1: 'haid_pita' di divisi nonaktif, tapi di user dikustom AKTIF [c=1, r=1]
        // 2. Nabrak 2: 'kesehatan' di divisi aktif full CRUD, tapi di user dikustom NONAKTIF (semua 0)
        // 3. Nabrak 3: 'olahraga' di divisi aktif full CRUD, tapi di user dikustom READ ONLY [c=0, r=1, u=0, d=0]
        // =========================================================================
        $responseCustom = $this->actingAs($adminInstansi)->post(route('admin.users.permissions', $user->id), [
            'use_custom_modules' => '1',
            'custom_permissions' => [
                'modules' => [
                    'haid_pita' => ['c' => '1', 'r' => '1', 'u' => '0', 'd' => '0'],
                    'kesehatan' => ['c' => '0', 'r' => '0', 'u' => '0', 'd' => '0'],
                    'olahraga' => ['c' => '0', 'r' => '1', 'u' => '0', 'd' => '0'],
                ],
                'can_add_user' => '0',
            ],
        ]);
        $responseCustom->assertRedirect();

        $user->refresh();

        // 1. Verifikasi Nabrak 1: 'haid_pita' HARUS AKTIF & BISA CREATE/READ (Prioritas User Kustom)
        $this->assertTrue($user->isModuleActive('haid_pita'), 'haid_pita harus aktif karena user dikustom khusus aktif meskipun divisi nonaktif');
        $this->assertTrue($user->canCreate('haid_pita'), 'user harus bisa create haid_pita sesuai kustom khusus');
        $this->assertTrue($user->canRead('haid_pita'), 'user harus bisa read haid_pita sesuai kustom khusus');
        $this->assertFalse($user->canDelete('haid_pita'), 'user tidak boleh delete haid_pita');

        // 2. Verifikasi Nabrak 2: 'kesehatan' HARUS NONAKTIF & DITOLAK CRUD (Prioritas User Kustom)
        $this->assertFalse($user->isModuleActive('kesehatan'), 'kesehatan harus nonaktif karena user dikustom nonaktif meskipun divisi aktif');
        $this->assertFalse($user->canCreate('kesehatan'), 'user tidak boleh create kesehatan karena modul dinonaktifkan di kustom user');
        $this->assertFalse($user->canRead('kesehatan'), 'user tidak boleh read kesehatan karena modul dinonaktifkan di kustom user');
        $this->assertFalse($user->canUpdate('kesehatan'), 'user tidak boleh update kesehatan');
        $this->assertFalse($user->canDelete('kesehatan'), 'user tidak boleh delete kesehatan');

        // 3. Verifikasi Nabrak 3: 'olahraga' HARUS READ ONLY (Prioritas User Kustom)
        $this->assertTrue($user->isModuleActive('olahraga'), 'olahraga harus aktif karena ada read di kustom user');
        $this->assertTrue($user->canRead('olahraga'), 'user harus bisa read olahraga');
        $this->assertFalse($user->canCreate('olahraga'), 'user tidak boleh create olahraga karena kustom user adalah Read Only meskipun divisi izinkan Create');
        $this->assertFalse($user->canUpdate('olahraga'), 'user tidak boleh update olahraga karena kustom user Read Only');
        $this->assertFalse($user->canDelete('olahraga'), 'user tidak boleh delete olahraga karena kustom user Read Only');

        // =========================================================================
        // 4. RESET KEMBALI KE "IKUTI DIVISI / INSTANSI" (use_custom_modules = 0)
        // =========================================================================
        $responseReset = $this->actingAs($adminInstansi)->post(route('admin.users.permissions', $user->id), [
            'use_custom_modules' => '0',
        ]);
        $responseReset->assertRedirect();

        $user->refresh();
        $this->assertNull($user->custom_modules);
        $this->assertNull($user->custom_permissions);

        // Setelah di-reset, user kembali mewarisi pengaturan divisi
        $this->assertFalse($user->isModuleActive('haid_pita'));
        $this->assertTrue($user->isModuleActive('kesehatan'));
        $this->assertTrue($user->canCreate('kesehatan'));
        $this->assertTrue($user->canDelete('kesehatan'));
        $this->assertTrue($user->canCreate('olahraga'));
    }

    public function test_distinct_duty_and_personal_crud_permissions_for_staff_accounts(): void
    {
        $adminInstansi = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Admin Sekolah',
            'username' => 'admin_sek',
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
        ]);

        $healthConsultant = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Konselor BK',
            'username' => 'konselor_bk',
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'level' => UserRole::HEALTH_CONSULTANT->value,
            'status' => UserStatus::AKTIF->value,
        ]);

        // Default: Health Consultant
        // Haid Pita Duty: Read=true (monitoring siswa/guru), Update=true (kelola pita/medis), Create=false
        $this->assertTrue($healthConsultant->canRead('haid_pita', 'duty'));
        $this->assertTrue($healthConsultant->canUpdate('haid_pita', 'duty'));
        $this->assertFalse($healthConsultant->canCreate('haid_pita', 'duty'));

        // Haid Pita Personal: Create=true (catat mandiri jika perempuan), Read=true
        $this->assertTrue($healthConsultant->canCreate('haid_pita', 'personal'));
        $this->assertTrue($healthConsultant->canRead('haid_pita', 'personal'));

        // Now test custom override with distinct duty vs personal
        // Misal: Admin mengizinkan duty: haid_pita Read only (tidak boleh update/stop medis),
        // dan personal: olahraga GPS run tracker Create & Read.
        $payload = [
            'use_custom_modules' => '1',
            'custom_permissions' => [
                'duty_modules' => [
                    'haid_pita' => ['r' => 1, 'u' => 0, 'c' => 0, 'd' => 0],
                    'olahraga' => ['r' => 1, 'u' => 0, 'c' => 0, 'd' => 0],
                ],
                'personal_modules' => [
                    'haid_pita' => ['c' => 1, 'r' => 1, 'u' => 0, 'd' => 0],
                    'olahraga' => ['c' => 1, 'r' => 1, 'u' => 1, 'd' => 0],
                ],
            ],
        ];

        $response = $this->actingAs($adminInstansi)->post(route('admin.users.permissions', $healthConsultant->id), $payload);
        $response->assertRedirect();

        $healthConsultant->refresh();
        $this->assertNotNull($healthConsultant->custom_permissions);
        $this->assertArrayHasKey('duty_modules', $healthConsultant->custom_permissions);
        $this->assertArrayHasKey('personal_modules', $healthConsultant->custom_permissions);

        // Verify Duty Scope
        $this->assertTrue($healthConsultant->canRead('haid_pita', 'duty'));
        $this->assertFalse($healthConsultant->canUpdate('haid_pita', 'duty'));
        $this->assertFalse($healthConsultant->canCreate('haid_pita', 'duty'));

        // Verify Personal Scope
        $this->assertTrue($healthConsultant->canCreate('haid_pita', 'personal'));
        $this->assertTrue($healthConsultant->canRead('haid_pita', 'personal'));
        $this->assertTrue($healthConsultant->canCreate('olahraga', 'personal'));
        $this->assertTrue($healthConsultant->canUpdate('olahraga', 'personal'));

        // Verify Effective Combined
        $effective = $healthConsultant->getCrudPermissions();
        $this->assertArrayHasKey('duty_modules', $effective);
        $this->assertArrayHasKey('personal_modules', $effective);
        $this->assertArrayHasKey('modules', $effective);
    }

    public function test_health_monitor_dashboard_renders_cleanly_without_orphan_sidebar_tags(): void
    {
        if (!Schema::hasTable('software_apps')) {
            Schema::create('software_apps', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name')->default('Aku Sehat');
                $table->string('version')->default('1.0.0');
                $table->string('description')->nullable();
                $table->string('developer')->default('Neura Cakrawira Solusi');
                $table->text('link')->nullable();
                $table->string('status')->default('active');
                $table->text('icon')->nullable();
                $table->string('platform')->default('web');
                $table->date('release_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('license_keys')) {
            Schema::create('license_keys', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('license_key')->unique();
                $table->uuid('instansi_id')->nullable()->index();
                $table->string('status')->default('active');
                $table->integer('kuota_pengguna')->default(100);
                $table->date('tanggal_berakhir')->nullable();
                $table->timestamps();
            });
        }

        \Illuminate\Support\Facades\DB::table('license_keys')->insert([
            'id' => (string) Str::uuid(),
            'license_key' => 'AKU-TEST-' . Str::random(8),
            'instansi_id' => $this->instansi->id,
            'status' => 'active',
            'kuota_pengguna' => 100,
            'tanggal_berakhir' => now()->addYear()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if (!Schema::hasTable('v_instansi_health_summary')) {
            Schema::create('v_instansi_health_summary', function (Blueprint $table) {
                $table->uuid('instansi_id')->nullable();
                $table->integer('total_member')->default(0);
                $table->integer('total_divisi')->default(0);
                $table->integer('total_kesehatan_tahun_ini')->default(0);
                $table->integer('total_hb_tahun_ini')->default(0);
            });
        }
        if (!Schema::hasTable('v_divisi_dashboard_stats')) {
            Schema::create('v_divisi_dashboard_stats', function (Blueprint $table) {
                $table->uuid('instansi_id')->nullable();
                $table->string('divisi_name')->nullable();
                $table->integer('total_member')->default(0);
                $table->integer('total_kesehatan')->default(0);
                $table->integer('total_hb')->default(0);
            });
        }

        $healthMonitor = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Health Monitor Tels',
            'username' => 'hm_tels_test_' . Str::random(5),
            'password' => bcrypt('password'),
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
            'instansi_id' => $this->instansi->id,
            'id_divisi' => $this->divisi->id,
            'is_custom_permissions' => true,
            'custom_permissions' => [
                'can_read' => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => false,
                'duty_modules' => [
                    'kesehatan' => ['c' => 0, 'r' => 1, 'u' => 0, 'd' => 0],
                    'hemoglobin' => ['c' => 0, 'r' => 1, 'u' => 0, 'd' => 0],
                    'haid_pita' => ['c' => 0, 'r' => 1, 'u' => 0, 'd' => 0],
                ],
            ],
        ]);

        $response = $this->actingAs($healthMonitor)->get(route('dashboard'));
        $response->assertStatus(200);

        // Sidebar and main content must be present
        $response->assertSee('Selamat Datang, Health Monitor Tels');
        $response->assertSee('Data Kesehatan');
        $response->assertSee('Data Hemoglobin');
        $response->assertSee('Siklus & Pita Siswi', false);

        // Ensure no unescaped raw HTML snippet from broken tags is visible
        $response->assertDontSee('class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200');
    }
}
