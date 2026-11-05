<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Enums\UserGender;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, BelongsToTenant;

    protected $table = 'users';
    
    protected $fillable = [
        'id_divisi',
        'instansi_id',
        'tgl',
        'username',
        'email',
        'google_id',
        'avatar',
        'password',
        'nama',
        'level',
        'status',
        'suspended_until',
        'ban_reason',
        'jk',
        'nomor_induk',
        'custom_modules',
        'custom_permissions',
        'is_tester',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
        'custom_modules' => 'array',
        'custom_permissions' => 'array',
        'is_tester' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'role_label',
        'avatar_url',
        'name',
        'active_modules',
        'color_cover',
        'module_permissions',
    ];

    /**
     * The "booted" method of the model.
     * Revokasi token dan sesi secara instan saat akun dinonaktifkan, dibanned, atau disuspend.
     */
    protected static function booted(): void
    {
        static::updated(function (User $user) {
            if ($user->wasChanged('status') && in_array($user->status, ['banned', 'suspended', 'nonaktif'])) {
                // 1. Revokasi semua Sanctum API Tokens
                $user->tokens()->delete();

                // 2. Revokasi sesi aktif jika menggunakan session driver database
                try {
                    if (config('session.driver') === 'database' && \Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                        \Illuminate\Support\Facades\DB::table('sessions')
                            ->where('user_id', $user->id)
                            ->delete();
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Gagal menghapus session pengguna: ' . $e->getMessage());
                }
            }
        });
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function getColorCoverAttribute(): ?string
    {
        return $this->divisi?->color_cover;
    }

    public function getModulePermissionsAttribute(): array
    {
        return $this->getCrudPermissions();
    }

    public function getNisAttribute()
    {
        return $this->attributes['nomor_induk'] ?? null;
    }

    public function setNisAttribute($value)
    {
        $this->attributes['nomor_induk'] = !empty($value) ? $value : null;
    }

    public function setIdDivisiAttribute($value)
    {
        $this->attributes['id_divisi'] = !empty($value) ? $value : null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = !empty($value) ? $value : null;
    }

    public function setNomorIndukAttribute($value)
    {
        $this->attributes['nomor_induk'] = !empty($value) ? $value : null;
    }

    public function setTglAttribute($value)
    {
        $this->attributes['tgl'] = !empty($value) ? $value : null;
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    public function kesehatan()
    {
        return $this->hasMany(Kesehatan::class, 'id_user');
    }

    public function hb()
    {
        return $this->hasMany(Hb::class, 'id_user');
    }

    public function peminjamanPita()
    {
        return $this->hasMany(PeminjamanPita::class, 'id_user');
    }

    public function dataHaid()
    {
        return $this->hasMany(DataHaid::class, 'id_user');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function getLicenseKeyAttribute()
    {
        return $this->instansi?->getActiveLicense();
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class, 'user_id');
    }

    public function vitalLogs()
    {
        return $this->hasMany(UserVitalLog::class, 'user_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function isAdmin()
    {
        return $this->level === UserRole::ADMIN->value;
    }

    public function isSuperAdmin()
    {
        return in_array(strtolower((string) $this->level), ['superadmin', 'super admin', strtolower(UserRole::SUPERADMIN->value)]);
    }

    public function isHealthMonitor()
    {
        return $this->level === UserRole::HEALTH_MONITOR->value;
    }

    public function isHealthConsultant()
    {
        return $this->level === UserRole::HEALTH_CONSULTANT->value;
    }

    public function isAdminInstansi()
    {
        return $this->level === UserRole::ADMIN_INSTANSI->value;
    }

    public function isMember()
    {
        return $this->level === UserRole::MEMBER->value;
    }

    public function isPerempuan()
    {
        return $this->jk === UserGender::PEREMPUAN->value;
    }

    public function isBanned()
    {
        return $this->status === UserStatus::BANNED->value;
    }

    public function isSuspended()
    {
        if ($this->status !== UserStatus::SUSPENDED->value) {
            return false;
        }

        if ($this->suspended_until && $this->suspended_until->isPast()) {
            // Masa suspend sudah selesai, pulihkan akun
            $this->status = UserStatus::AKTIF->value;
            $this->suspended_until = null;
            $this->save();
            return false;
        }

        return true;
    }

    public function isAccountActive()
    {
        return !$this->isBanned() && !$this->isSuspended();
    }

    public function getRoleLabelAttribute()
    {
        if ($this->instansi) {
            return $this->instansi->getRoleLabel($this->level);
        }
        return $this->level;
    }

    public function getAvatarUrlAttribute()
    {
        if (empty($this->avatar)) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset('storage/' . ltrim($this->avatar, '/'));
    }

    public function isTester(): bool
    {
        return (bool) $this->is_tester;
    }

    public function isModuleActive($moduleKey)
    {
        // 0. SuperAdmin Master Feature Flag (Global Override)
        if (!\App\Models\GlobalModuleConfig::isUserAllowedToAccess($moduleKey, $this)) {
            return false;
        }

        // 1. PRIORITAS TERTINGGI: Kustomisasi Khusus Akun Pengguna (User Custom Override)
        // A. Cek per-modul CRUD permission jika dikustomisasi secara spesifik pada user
        if (is_array($this->custom_permissions) && isset($this->custom_permissions['modules'][$moduleKey])) {
            $modPerm = $this->custom_permissions['modules'][$moduleKey];
            $hasAnyCrud = !empty($modPerm['c']) || !empty($modPerm['r']) || !empty($modPerm['u']) || !empty($modPerm['d'])
                || !empty($modPerm['create']) || !empty($modPerm['read']) || !empty($modPerm['update']) || !empty($modPerm['delete']);

            if (is_array($this->custom_modules)) {
                return in_array($moduleKey, $this->custom_modules) || $hasAnyCrud;
            }

            return $hasAnyCrud;
        }

        // B. Cek daftar modul custom_modules akun user
        if (is_array($this->custom_modules)) {
            return in_array($moduleKey, $this->custom_modules);
        }

        // 2. Division level settings (Hanya berlaku jika user TIDAK memiliki kustom khusus)
        if ($this->divisi) {
            return $this->divisi->isModuleActive($moduleKey);
        }

        // 3. Institution global default (Fallback terakhir jika divisi & user tidak dikustom)
        if ($this->instansi) {
            return $this->instansi->isModuleActive($moduleKey);
        }

        return true;
    }

    /**
     * Mengambil daftar key modul yang aktif untuk user ini.
     *
     * @return array
     */
    public function getActiveModulesAttribute(): array
    {
        return $this->getActiveModules();
    }

    public function getActiveModules(): array
    {
        $catalog = array_keys(\App\Models\GlobalModuleConfig::getModuleCatalog());
        $active = [];
        foreach ($catalog as $key) {
            if ($this->isModuleActive($key)) {
                $active[] = $key;
            }
        }
        return $active;
    }

    /**
     * Cek apakah modul sedang dalam perbaikan/pemeliharaan teknis oleh SuperAdmin.
     */
    public function isModuleInMaintenance($moduleKey): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }
        return \App\Models\GlobalModuleConfig::isModuleInMaintenance($moduleKey);
    }

    /**
     * Memeriksa apakah user memiliki izin untuk menambahkan/mengimpor user baru.
     * Admin Instansi selalu memiliki akses.
     * Role lain (Admin PMR, Health Monitor, Health Consultant) ditentukan oleh Admin Instansi.
     */
    public function canAddUser(): bool
    {
        // 1. Superadmin & Admin Instansi selalu memiliki hak akses penuh
        if (in_array($this->level, [UserRole::SUPERADMIN->value, 'Superadmin', UserRole::ADMIN_INSTANSI->value])) {
            return true;
        }

        // Jika izin Create operasional dicabut secara eksplisit, wewenang tambah user otomatis gugur
        if (!$this->canCreate()) {
            return false;
        }

        // 2. Cek Custom Permission individual user jika ada override
        if (is_array($this->custom_permissions) && array_key_exists('can_add_user', $this->custom_permissions)) {
            return (bool) $this->custom_permissions['can_add_user'];
        }

        // 3. Cek Permission Divisi jika divisi memiliki perizinan spesifik
        if ($this->divisi && is_array($this->divisi->module_permissions) && array_key_exists('can_add_user', $this->divisi->module_permissions)) {
            return (bool) $this->divisi->module_permissions['can_add_user'];
        }

        // 4. Cek Kebijakan Hak Akses Instansi (Global)
        $instansi = $this->instansi ?: ($this->sekolah ?: Instansi::find($this->instansi_id));
        if ($instansi) {
            $instansiPerms = $instansi->module_permissions ?? [];

            // Admin PMR / Koordinator Unit: default true jika belum diatur, atau sesuai checkbox instansi
            if ($this->level === UserRole::ADMIN->value) {
                return !isset($instansiPerms['admin_can_add_user']) || !empty($instansiPerms['admin_can_add_user']);
            }

            // Health Monitor (Petugas Medis / UKS): default false kecuali diaktifkan admin instansi
            if ($this->level === UserRole::HEALTH_MONITOR->value) {
                return !empty($instansiPerms['health_monitor_can_add_user']);
            }

            // Health Consultant (Konselor / BK): default false kecuali diaktifkan admin instansi
            if ($this->level === UserRole::HEALTH_CONSULTANT->value) {
                return !empty($instansiPerms['health_consultant_can_add_user']);
            }
        }

        return false;
    }

    /**
     * Memeriksa apakah member diizinkan menginput tanda vital mandiri (tensi, jam tidur, mood, air).
     */
    public function canInputVitals(): bool
    {
        // Non-member (Admin, Health Monitor, Consultant) selalu berhak
        if ($this->isAdminInstansi() || $this->isSuperAdmin() || $this->isAdmin() || $this->isHealthMonitor() || $this->isHealthConsultant()) {
            return true;
        }

        // Akun member wajib memiliki hak akses operasional create
        if (!$this->canCreate()) {
            return false;
        }

        // Cek Kebijakan Hak Akses Instansi
        $instansi = $this->instansi ?: ($this->sekolah ?: Instansi::find($this->instansi_id));
        if ($instansi) {
            $perms = $instansi->module_permissions ?? [];
            return !empty($perms['member_can_input_vitals']);
        }

        return false;
    }

    /**
     * Memeriksa apakah member diizinkan melihat papan peringkat (leaderboard) lari.
     */
    public function canViewLeaderboard(): bool
    {
        // Non-member (Admin, Health Monitor, Consultant) selalu berhak
        if ($this->isAdminInstansi() || $this->isSuperAdmin() || $this->isAdmin() || $this->isHealthMonitor() || $this->isHealthConsultant()) {
            return true;
        }

        $instansi = $this->instansi ?: ($this->sekolah ?: Instansi::find($this->instansi_id));
        if ($instansi) {
            $perms = $instansi->module_permissions ?? [];
            return !empty($perms['member_can_view_leaderboard']);
        }

        return false;
    }

    /**
     * Memeriksa hak akses CRUD (Create, Read, Update, Delete) operasional, baik global maupun spesifik per modul.
     * Mengikuti hirarki: SuperAdmin/AdminInstansi -> Custom User -> Divisi -> Instansi -> Role Default.
     *
     * @param string $action 'create' | 'read' | 'update' | 'delete'
     * @param string|null $moduleKey (e.g. 'kesehatan', 'hemoglobin', 'konsultasi', dsb)
     * @return bool
     */
    public function hasCrudPermission(string $action, ?string $moduleKey = null, string $scope = 'any'): bool
    {
        $action = strtolower($action);
        $actionShort = match ($action) {
            'create', 'c' => 'c',
            'read', 'r' => 'r',
            'update', 'u' => 'u',
            'delete', 'd' => 'd',
            default => 'r',
        };
        $actionLong = match ($actionShort) {
            'c' => 'create',
            'r' => 'read',
            'u' => 'update',
            'd' => 'delete',
        };
        $key = 'can_' . $actionLong;

        // 1. Superadmin & Admin Instansi selalu memiliki hak akses CRUD penuh ke semua modul
        if ($this->isSuperAdmin() || $this->isAdminInstansi()) {
            return true;
        }

        // Modul nonaktif? Jika $moduleKey diberikan dan modul nonaktif untuk pengguna ini, akses CRUD ditolak
        if ($moduleKey && !$this->isModuleActive($moduleKey)) {
            return false;
        }

        // 2. Custom Permissions override khusus pada akun user bersangkutan
        if (is_array($this->custom_permissions)) {
            // A. Scope-specific check if requested ('duty' or 'personal')
            if ($scope === 'duty' && $moduleKey && isset($this->custom_permissions['duty_modules'][$moduleKey])) {
                $mod = $this->custom_permissions['duty_modules'][$moduleKey];
                if (isset($mod[$actionShort])) {
                    return (bool) $mod[$actionShort];
                }
                if (isset($mod[$actionLong])) {
                    return (bool) $mod[$actionLong];
                }
            } elseif ($scope === 'personal' && $moduleKey && isset($this->custom_permissions['personal_modules'][$moduleKey])) {
                $mod = $this->custom_permissions['personal_modules'][$moduleKey];
                if (isset($mod[$actionShort])) {
                    return (bool) $mod[$actionShort];
                }
                if (isset($mod[$actionLong])) {
                    return (bool) $mod[$actionLong];
                }
            }

            // B. Cek izin per modul spesifik umum (modules matrix)
            if ($moduleKey && isset($this->custom_permissions['modules'][$moduleKey])) {
                $mod = $this->custom_permissions['modules'][$moduleKey];
                if (isset($mod[$actionShort])) {
                    return (bool) $mod[$actionShort];
                }
                if (isset($mod[$actionLong])) {
                    return (bool) $mod[$actionLong];
                }
            }

            // C. Cek izin fallback global user
            if (array_key_exists($key, $this->custom_permissions)) {
                return (bool) $this->custom_permissions[$key];
            }
        }

        // 3. Permission konfigurasi pada divisi pengguna
        if ($this->divisi && is_array($this->divisi->module_permissions)) {
            // A. Cek izin per modul pada divisi
            if ($moduleKey && isset($this->divisi->module_permissions['modules'][$moduleKey])) {
                $mod = $this->divisi->module_permissions['modules'][$moduleKey];
                if (isset($mod[$actionShort])) {
                    return (bool) $mod[$actionShort];
                }
                if (isset($mod[$actionLong])) {
                    return (bool) $mod[$actionLong];
                }
            }

            // B. Cek izin fallback global divisi
            if (array_key_exists($key, $this->divisi->module_permissions)) {
                return (bool) $this->divisi->module_permissions[$key];
            }
        }

        // 4. Permission global pada instansi jika diatur
        $instansi = $this->instansi ?: ($this->sekolah ?: Instansi::find($this->instansi_id));
        if ($instansi && is_array($instansi->module_permissions)) {
            if ($moduleKey && isset($instansi->module_permissions['modules'][$moduleKey])) {
                $mod = $instansi->module_permissions['modules'][$moduleKey];
                if (isset($mod[$actionShort])) {
                    return (bool) $mod[$actionShort];
                }
                if (isset($mod[$actionLong])) {
                    return (bool) $mod[$actionLong];
                }
            }
            if (array_key_exists($key, $instansi->module_permissions)) {
                return (bool) $instansi->module_permissions[$key];
            }
        }

        // 5. Default bawaan sistem berdasarkan role pengguna dan modul
        return self::getDefaultModulePermission($this->level, $actionLong, $moduleKey, $scope);
    }

    /**
     * Cek apakah akun ini adalah role staf operasional/tugas institusi (Health Monitor, Health Consultant, Admin PMR).
     */
    public function isStaffDutyRole(): bool
    {
        return in_array($this->level, [
            UserRole::HEALTH_MONITOR->value,
            UserRole::HEALTH_CONSULTANT->value,
            UserRole::ADMIN->value,
            'Health Monitor',
            'Health Consultant',
            'Admin',
        ], true);
    }

    /**
     * Menghitung nilai default izin per modul sesuai peran (role) pengguna dan scope (duty vs personal).
     */
    public static function getDefaultModulePermission(?string $role, string $action, ?string $moduleKey = null, string $scope = 'any'): bool
    {
        $action = strtolower($action);

        if (in_array($role, [UserRole::SUPERADMIN->value, 'Superadmin', UserRole::ADMIN_INSTANSI->value, UserRole::ADMIN->value])) {
            return true;
        }

        // Scope 1: Wewenang Akun Tugas (Operasional & Monitoring Institusi)
        if ($scope === 'duty') {
            if ($role === UserRole::MEMBER->value) {
                // Member tidak memiliki wewenang tugas terhadap anggota lain
                return false;
            }

            if ($role === UserRole::HEALTH_MONITOR->value) {
                if ($moduleKey === null || in_array($moduleKey, ['kesehatan', 'hemoglobin', 'haid_pita', 'olahraga', 'vitalitas', 'nutrisi', 'ergonomi', 'vaksinasi'])) {
                    return match ($action) {
                        'create', 'read', 'update' => true,
                        'delete' => false,
                        default => false,
                    };
                }
                if (in_array($moduleKey, ['konsultasi', 'burnout'])) {
                    return $action === 'read';
                }
                return false;
            }

            if ($role === UserRole::HEALTH_CONSULTANT->value) {
                if ($moduleKey === null || in_array($moduleKey, ['konsultasi', 'burnout'])) {
                    return match ($action) {
                        'create', 'read', 'update' => true,
                        'delete' => false,
                        default => false,
                    };
                }
                if ($moduleKey === 'haid_pita') {
                    // Health Consultant monitoring siswi/guru dan verifikasi/hentikan pita haid medis
                    return in_array($action, ['read', 'update'], true);
                }
                return $action === 'read';
            }

            return false;
        }

        // Scope 2: Hak Akses Personal / Mandiri (Penggunaan Pribadi Diri Sendiri)
        if ($scope === 'personal') {
            if ($moduleKey === null || in_array($moduleKey, ['vitalitas', 'olahraga', 'nutrisi', 'burnout', 'ergonomi', 'haid_pita', 'konsultasi'])) {
                return match ($action) {
                    'create', 'read' => true,
                    'update' => true,
                    'delete' => false,
                    default => false,
                };
            }
            return $action === 'read';
        }

        // Scope 3: Default 'any' (Backward Compatible)
        if ($role === UserRole::HEALTH_MONITOR->value) {
            // Health Monitor (Guru Penjas / Petugas UKS): fokus pada fisik, medis, olahraga & lifestyle
            if ($moduleKey === null || in_array($moduleKey, ['kesehatan', 'hemoglobin', 'haid_pita', 'olahraga', 'vitalitas', 'nutrisi', 'ergonomi', 'vaksinasi'])) {
                return match ($action) {
                    'create', 'read', 'update' => true,
                    'delete' => false, // Proteksi pencegahan penghapusan tidak sengaja
                    default => false,
                };
            }
            // Modul bimbingan mental/konseling (SafeSpace & Burnout): hanya Read saja bagi Health Monitor
            if (in_array($moduleKey, ['konsultasi', 'burnout'])) {
                return $action === 'read';
            }
            return false;
        }

        if ($role === UserRole::HEALTH_CONSULTANT->value) {
            // Health Consultant (Guru BK / Konselor): fokus pada SafeSpace & evaluasi burnout mental
            if ($moduleKey === null || in_array($moduleKey, ['konsultasi', 'burnout'])) {
                return match ($action) {
                    'create', 'read', 'update' => true,
                    'delete' => false,
                    default => false,
                };
            }
            if ($moduleKey === 'haid_pita') {
                return in_array($action, ['read', 'update'], true);
            }
            // Modul klinis fisik lainnya: Read-only (untuk meninjau kondisi kesehatan siswi yang dikonseling)
            return $action === 'read';
        }

        if ($role === UserRole::MEMBER->value) {
            // Member: entri mandiri (vitalitas, olahraga, hidrasi, booking)
            if ($moduleKey === null || in_array($moduleKey, ['vitalitas', 'olahraga', 'nutrisi', 'burnout', 'ergonomi', 'haid_pita', 'konsultasi'])) {
                return match ($action) {
                    'create', 'read' => true,
                    'update', 'delete' => false,
                    default => false,
                };
            }
            // Rekam medis fisik klinis resmi: hanya Read riwayat
            return $action === 'read';
        }

        return false;
    }

    public function canCreate(?string $module = null, string $scope = 'any'): bool
    {
        return $this->hasCrudPermission('create', $module, $scope);
    }

    public function canRead(?string $module = null, string $scope = 'any'): bool
    {
        return $this->hasCrudPermission('read', $module, $scope);
    }

    public function canUpdate(?string $module = null, string $scope = 'any'): bool
    {
        return $this->hasCrudPermission('update', $module, $scope);
    }

    public function canDelete(?string $module = null, string $scope = 'any'): bool
    {
        return $this->hasCrudPermission('delete', $module, $scope);
    }

    public function getCrudPermissions(?string $module = null): array
    {
        $catalog = GlobalModuleConfig::getModuleCatalog();
        $modulesCrud = [];
        $dutyModules = [];
        $personalModules = [];

        foreach ($catalog as $key => $info) {
            $dutyModules[$key] = [
                'c' => $this->hasCrudPermission('create', $key, 'duty'),
                'r' => $this->hasCrudPermission('read', $key, 'duty'),
                'u' => $this->hasCrudPermission('update', $key, 'duty'),
                'd' => $this->hasCrudPermission('delete', $key, 'duty'),
            ];

            $personalModules[$key] = [
                'c' => $this->hasCrudPermission('create', $key, 'personal'),
                'r' => $this->hasCrudPermission('read', $key, 'personal'),
                'u' => $this->hasCrudPermission('update', $key, 'personal'),
                'd' => $this->hasCrudPermission('delete', $key, 'personal'),
            ];

            $modulesCrud[$key] = [
                'c' => $this->hasCrudPermission('create', $key, 'any'),
                'r' => $this->hasCrudPermission('read', $key, 'any'),
                'u' => $this->hasCrudPermission('update', $key, 'any'),
                'd' => $this->hasCrudPermission('delete', $key, 'any'),
            ];
        }

        return [
            'can_create' => $this->canCreate($module),
            'can_read' => $this->canRead($module),
            'can_update' => $this->canUpdate($module),
            'can_delete' => $this->canDelete($module),
            'can_add_user' => $this->canAddUser(),
            'modules' => $modulesCrud,
            'duty_modules' => $dutyModules,
            'personal_modules' => $personalModules,
        ];
    }
}
