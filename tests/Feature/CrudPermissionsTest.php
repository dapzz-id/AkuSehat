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
        $this->assertEquals([
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => false,
            'can_add_user' => true,
        ], $this->divisi->module_permissions);
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
        $this->assertEquals([
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => true,
            'can_add_user' => true,
        ], $targetUser->custom_permissions);

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
}
