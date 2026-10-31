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

        // 1. User level custom override
        if (!is_null($this->custom_modules)) {
            return in_array($moduleKey, $this->custom_modules);
        }

        // 2. Division level settings
        if ($this->divisi && !is_null($this->divisi->active_modules)) {
            return in_array($moduleKey, $this->divisi->active_modules);
        }

        // 3. Institution global default
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
}
