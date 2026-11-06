<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Instansi;
use App\Models\Divisi;
use App\Models\LicenseKey;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Enums\UserGender;
use App\Modules\SuperAdmin\LicenseKey\Services\LicenseKeyService;
use App\Modules\User\ManageUser\Services\DashboardService;

class LicenseQuotaAndDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('instansi')) {
            Schema::create('instansi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_instansi')->nullable();
                $table->string('jenis_instansi')->default('Sekolah');
                $table->string('email')->nullable();
                $table->string('invite_code')->nullable();
                $table->string('status')->default('aktif');
                $table->uuid('license_id_active')->nullable();
                $table->json('active_modules')->nullable();
                $table->json('module_permissions')->nullable();
                $table->json('custom_role_labels')->nullable();
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
                $table->boolean('is_custom_permissions')->default(false);
                $table->boolean('is_tester')->default(false);
                $table->dateTime('suspended_until')->nullable();
                $table->string('ban_reason')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('license_keys')) {
            Schema::create('license_keys', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable()->index();
                $table->string('key')->unique();
                $table->string('status')->default('active');
                $table->integer('kuota_pengguna')->default(100);
                $table->date('tanggal_berakhir')->nullable();
                $table->timestamps();
            });
        }

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

        if (!Schema::hasTable('global_module_configs')) {
            Schema::create('global_module_configs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('module_key')->unique();
                $table->string('module_name')->default('Modul');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('peminjaman_pita')) {
            Schema::create('peminjaman_pita', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('user_id')->nullable();
                $table->string('status')->default('dipinjam');
                $table->date('estimasi_selesai_haid')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_role')->nullable();
                $table->string('action');
                $table->string('category')->default('system');
                $table->text('description')->nullable();
                $table->json('properties')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device')->nullable();
                $table->timestamps();
            });
        }

        // Clean tables
        DB::table('users')->delete();
        DB::table('divisi')->delete();
        DB::table('license_keys')->delete();
        DB::table('instansi')->delete();

        $this->ensureViewsExist();
    }

    private function ensureViewsExist(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('DROP VIEW IF EXISTS v_instansi_health_summary;');
            DB::statement("
                CREATE VIEW v_instansi_health_summary AS
                SELECT 
                    i.id AS instansi_id,
                    i.nama_instansi,
                    i.jenis_instansi,
                    COUNT(DISTINCT d.id) AS total_divisi,
                    COUNT(DISTINCT CASE WHEN u.level NOT IN ('Admin Instansi', 'SuperAdmin', 'admin_instansi', 'superadmin') THEN u.id END) AS total_member,
                    COUNT(DISTINCT u.id) AS total_all_users,
                    0 AS total_kesehatan_tahun_ini,
                    0 AS total_hb_tahun_ini,
                    0 AS total_anemia_tahun_ini,
                    0 AS total_normal_imt_tahun_ini,
                    0 AS total_hipertensi_tahun_ini
                FROM instansi i
                LEFT JOIN divisi d ON d.instansi_id = i.id
                LEFT JOIN users u ON u.instansi_id = i.id
                GROUP BY i.id, i.nama_instansi, i.jenis_instansi;
            ");

            DB::statement('DROP VIEW IF EXISTS v_divisi_dashboard_stats;');
            DB::statement("
                CREATE VIEW v_divisi_dashboard_stats AS
                SELECT 
                    d.id AS divisi_id,
                    d.instansi_id,
                    d.divisi_name,
                    d.kategori,
                    d.color_cover,
                    d.created_at,
                    COUNT(DISTINCT CASE WHEN u.level NOT IN ('Admin Instansi', 'SuperAdmin', 'admin_instansi', 'superadmin') THEN u.id END) AS total_member,
                    0 AS total_kesehatan_tahun_ini,
                    0 AS total_hb_tahun_ini,
                    0 AS avg_imt,
                    0 AS avg_hb,
                    0 AS total_anemia_tahun_ini
                FROM divisi d
                LEFT JOIN users u ON u.id_divisi = d.id AND u.level NOT IN ('Admin Instansi', 'SuperAdmin', 'admin_instansi', 'superadmin')
                GROUP BY d.id, d.instansi_id, d.divisi_name, d.kategori, d.color_cover, d.created_at;
            ");
        }
    }

    public function test_license_quota_counts_all_non_admin_users_accurately(): void
    {
        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMKS Telekomunikasi Telesandi Bekasi',
            'jenis_instansi' => 'Sekolah',
        ]);

        $divisiGuru = Divisi::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'divisi_name' => 'Guru',
            'kategori' => 'Staf',
        ]);

        $divisiKelas = Divisi::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'divisi_name' => 'X RPL 1',
            'kategori' => 'Siswa',
        ]);

        // 1 Admin Instansi (tidak memotong kuota)
        $adminInstansi = User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nama' => 'Admin SMKS Telesandi',
            'username' => 'admin_telesandi',
            'email' => 'admin@telesandi.sch.id',
            'level' => UserRole::ADMIN_INSTANSI->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // 4 Akun yang memotong kuota lisensi:
        // 1. Health Consultant
        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisiGuru->id,
            'nama' => 'Health Consultant Tels',
            'username' => 'hc_telesandi',
            'email' => 'hc@telesandi.sch.id',
            'level' => UserRole::HEALTH_CONSULTANT->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // 2. Health Monitor
        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisiGuru->id,
            'nama' => 'Health Monitor Tels',
            'username' => 'hm_telesandi',
            'email' => 'hm@telesandi.sch.id',
            'level' => UserRole::HEALTH_MONITOR->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // 3. PMR (Admin UKS)
        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisiKelas->id,
            'nama' => 'PMR',
            'username' => 'pmr_telesandi',
            'email' => 'pmr@telesandi.sch.id',
            'level' => UserRole::ADMIN->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // 4. Siswa / Member
        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'id_divisi' => $divisiKelas->id,
            'nama' => 'Siswa Tels',
            'username' => 'siswa_telesandi',
            'email' => 'siswa@telesandi.sch.id',
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        // Buat 1 lisensi kuota 34
        $license = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-TEST-34KUOTA',
            'kuota_pengguna' => 34,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addYear(),
        ]);

        $instansi->update(['license_id_active' => $license->id]);

        // Verifikasi count pada model Instansi
        $this->assertEquals(4, $instansi->getCountableUsersCount());

        // Verifikasi used quota pada model LicenseKey
        $this->assertEquals(4, $license->getUsedQuota());
        $this->assertEquals(30, $license->getRemainingQuota());

        // Verifikasi pada DashboardService
        $dashboardService = app(DashboardService::class);
        $dashboardData = $dashboardService->getAdminDashboardData($adminInstansi);
        $this->assertEquals(4, $dashboardData['data']['total_member']);

        // Login sebagai Admin Instansi dan akses dashboard
        $response = $this->actingAs($adminInstansi)->get(route('dashboard'));
        $response->assertOk();
        // Memastikan widget status kuota menampilkan 4 / 34
        $response->assertSee('4 / 34');
    }

    public function test_superadmin_cannot_delete_active_license_when_in_use_and_no_other_license(): void
    {
        $superadmin = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'superadmin@akusehat.id',
            'level' => UserRole::SUPERADMIN->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMK Maju Bersama',
            'jenis_instansi' => 'Sekolah',
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'nama' => 'Member Aktif',
            'username' => 'member_aktif',
            'email' => 'member@maju.sch.id',
            'level' => UserRole::MEMBER->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $license = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-ONLY-ONE-LIC',
            'kuota_pengguna' => 10,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths(6),
        ]);
        $instansi->update(['license_id_active' => $license->id]);

        $licenseService = app(LicenseKeyService::class);
        $canDeleteCheck = $licenseService->canDeleteLicense($license);

        $this->assertFalse($canDeleteCheck['can_delete']);
        $this->assertEquals('blocked_in_use', $canDeleteCheck['action_type']);

        // Request delete oleh superadmin
        $response = $this->actingAs($superadmin)->delete(route('superadmin.license.destroy', $license->id));
        $response->assertRedirect(route('superadmin.license.index'));
        $response->assertSessionHas('error');

        // Lisensi tidak boleh terhapus
        $this->assertDatabaseHas('license_keys', ['id' => $license->id]);
    }

    public function test_superadmin_cannot_delete_license_if_other_license_quota_is_full_or_insufficient(): void
    {
        $superadmin = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Super Administrator',
            'username' => 'superadmin_2',
            'email' => 'superadmin2@akusehat.id',
            'level' => UserRole::SUPERADMIN->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMA Negeri 1',
            'jenis_instansi' => 'Sekolah',
        ]);

        // Buat 4 user aktif di instansi
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'id' => (string) Str::uuid(),
                'instansi_id' => $instansi->id,
                'nama' => "Siswa {$i}",
                'username' => "siswa_num_{$i}",
                'email' => "siswa{$i}@sman1.sch.id",
                'level' => UserRole::MEMBER->value,
                'status' => UserStatus::AKTIF->value,
                'jk' => UserGender::LAKI_LAKI->value,
            ]);
        }

        // Lisensi A: kuota 8
        $licA = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-LIC-A-8',
            'kuota_pengguna' => 8,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths(6),
        ]);

        // Lisensi B: kuota 2 (tidak cukup menampung 4 user)
        $licB = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-LIC-B-2',
            'kuota_pengguna' => 2,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths(12),
        ]);

        $instansi->update(['license_id_active' => $licA->id]);

        $licenseService = app(LicenseKeyService::class);
        $check = $licenseService->canDeleteLicense($licA);

        $this->assertFalse($check['can_delete']);
        $this->assertEquals('blocked_overflow', $check['action_type']);

        $response = $this->actingAs($superadmin)->delete(route('superadmin.license.destroy', $licA->id));
        $response->assertRedirect(route('superadmin.license.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('license_keys', ['id' => $licA->id]);
    }

    public function test_superadmin_can_delete_license_when_other_license_has_enough_quota_and_transfers(): void
    {
        $superadmin = User::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Super Administrator',
            'username' => 'superadmin_3',
            'email' => 'superadmin3@akusehat.id',
            'level' => UserRole::SUPERADMIN->value,
            'status' => UserStatus::AKTIF->value,
            'jk' => UserGender::LAKI_LAKI->value,
        ]);

        $instansi = Instansi::create([
            'id' => (string) Str::uuid(),
            'nama_instansi' => 'SMA Nusantara',
            'jenis_instansi' => 'Sekolah',
        ]);

        // Buat 4 user aktif
        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'id' => (string) Str::uuid(),
                'instansi_id' => $instansi->id,
                'nama' => "Anggota {$i}",
                'username' => "anggota_num_{$i}",
                'email' => "anggota{$i}@nusantara.sch.id",
                'level' => UserRole::MEMBER->value,
                'status' => UserStatus::AKTIF->value,
                'jk' => UserGender::LAKI_LAKI->value,
            ]);
        }

        // Lisensi A: kuota 1 (yang ingin dihapus)
        $licA = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-LIC-A-1',
            'kuota_pengguna' => 1,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths(6),
        ]);

        // Lisensi B: kuota 34 (mencukupi 4 user)
        $licB = LicenseKey::create([
            'id' => (string) Str::uuid(),
            'instansi_id' => $instansi->id,
            'key' => 'NCS-LIC-B-34',
            'kuota_pengguna' => 34,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths(12),
        ]);

        $instansi->update(['license_id_active' => $licA->id]);

        $licenseService = app(LicenseKeyService::class);
        $check = $licenseService->canDeleteLicense($licA);

        $this->assertTrue($check['can_delete']);
        $this->assertEquals('transfer', $check['action_type']);
        $this->assertEquals($licB->id, $check['target_license']->id);

        $response = $this->actingAs($superadmin)->delete(route('superadmin.license.destroy', $licA->id));
        $response->assertRedirect(route('superadmin.license.index'));
        $response->assertSessionHas('success');

        // Lisensi A berhasil dihapus
        $this->assertDatabaseMissing('license_keys', ['id' => $licA->id]);
        $this->assertDatabaseHas('license_keys', ['id' => $licB->id]);

        // license_id_active instansi otomatis dialihkan ke Lic B
        $instansi->refresh();
        $this->assertEquals($licB->id, $instansi->license_id_active);
    }
}
