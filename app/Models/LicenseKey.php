<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\SuperAdmin\LicenseKey\Enums\LicenseStatus;

class LicenseKey extends Model
{
    use HasUuids;

    protected $table = 'license_keys';
    const STATUS_ACTIVE = LicenseStatus::ACTIVE->value;
    const STATUS_EXPIRED = LicenseStatus::EXPIRED->value;

    protected $fillable = [
        'instansi_id',
        'key',
        'kuota_pengguna',
        'status',
        'tanggal_berakhir'
    ];

    protected $casts = [
        'tanggal_berakhir' => 'date',
    ];

    public function instansiAktif()
    {
        return $this->hasMany(Instansi::class, 'license_id_active');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'instansi_id', 'instansi_id');
    }

    /**
     * Anggota/siswa yang dihitung memotong kuota lisensi (Admin Instansi & SuperAdmin tidak memotong kuota).
     */
    public function countableMembers()
    {
        return $this->hasMany(User::class, 'instansi_id', 'instansi_id')
            ->whereNotIn('level', [
                \App\Modules\User\Enums\UserRole::ADMIN_INSTANSI->value,
                \App\Modules\User\Enums\UserRole::SUPERADMIN->value,
                'Admin Instansi',
                'SuperAdmin',
            ]);
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && $this->tanggal_berakhir && $this->tanggal_berakhir->copy()->endOfDay()->gte(now());
    }

    /**
     * Menghitung berapa kuota dari lisensi ini yang telah terpakai oleh anggota/siswa/staf instansi.
     * Admin Instansi dan SuperAdmin tidak memotong kuota.
     */
    public function getUsedQuota(): int
    {
        $instansi = $this->sekolah;
        if (!$instansi) {
            return 0;
        }

        // Total anggota/staf aktif di instansi yang memotong kuota lisensi
        $totalMembers = $instansi->getCountableUsersCount();

        // Ambil semua lisensi aktif instansi terurut berdasarkan tanggal dibuat
        $allLicenses = $instansi->licenseKeys()
            ->where('status', self::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->orderBy('created_at', 'asc')
            ->get();

        $accumulatedPrior = 0;
        foreach ($allLicenses as $lic) {
            if ($lic->id === $this->id) {
                $availableMembers = max(0, $totalMembers - $accumulatedPrior);
                return min((int)$this->kuota_pengguna, $availableMembers);
            }
            $accumulatedPrior += (int) $lic->kuota_pengguna;
        }

        return min((int)$this->kuota_pengguna, max(0, $totalMembers));
    }

    /**
     * Menghitung sisa kuota yang masih tersedia pada lisensi ini
     */
    public function getRemainingQuota(): int
    {
        return max(0, (int) $this->kuota_pengguna - $this->getUsedQuota());
    }

    /**
     * Cek apakah license sudah expired
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED || ($this->tanggal_berakhir && now()->gt($this->tanggal_berakhir->copy()->endOfDay()));
    }

    /**
     * Get sisa hari sebelum expired
     */
    public function getDaysRemaining()
    {
        if ($this->isExpired() || !$this->tanggal_berakhir) {
            return 0;
        }
        return (int) max(0, ceil(now()->diffInDays($this->tanggal_berakhir->copy()->endOfDay(), false)));
    }

    /**
     * Tandai license sebagai expired jika sudah lewat tanggal berakhir
     */
    public function markAsExpired()
    {
        if ($this->isExpired() && $this->status === self::STATUS_ACTIVE) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    /**
     * Scope: Get license aktif untuk instansi tertentu
     * Satu instansi bisa punya banyak license, tapi hanya 1 yang aktif
     */
    public static function getActiveLicenseByInstansi($instansiId)
    {
        return self::where('instansi_id', $instansiId)
            ->where('status', self::STATUS_ACTIVE)
            ->where('tanggal_berakhir', '>=', \Carbon\Carbon::now())
            ->orderBy('tanggal_berakhir', 'desc')
            ->first();
    }

    /**
     * Check apakah instansi memiliki license yang aktif
     */
    public static function hasActiveLicense($instansiId)
    {
        return self::getActiveLicenseByInstansi($instansiId) !== null;
    }

    /**
     * Get riwayat license untuk instansi tertentu
     */
    public static function getHistoryByInstansi($instansiId)
    {
        return self::where('instansi_id', $instansiId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Nonaktifkan semua license aktif instansi sebelum buat yang baru
     * Ini opsional, tergantung business logic:
     * - Jika 1 instansi hanya boleh 1 license aktif: gunakan ini
     * - Jika boleh overlap: skip fungsi ini
     */
    public static function deactivateOtherLicenses($instansiId, $exceptLicenseId = null)
    {
        $query = self::where('instansi_id', $instansiId)
            ->where('status', self::STATUS_ACTIVE);
        
        if ($exceptLicenseId) {
            $query->where('id', '!=', $exceptLicenseId);
        }

        $query->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Boot method untuk model
     * Otomatis tandai sebagai expired saat menyimpan jika tanggal berakhir sudah lewat
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($licenseKey) {
            if ($licenseKey->tanggal_berakhir && $licenseKey->tanggal_berakhir->copy()->endOfDay()->isPast()) {
                $licenseKey->status = self::STATUS_EXPIRED;
            }
        });
    }

    /**
     * Generate unique license key dengan format NCS-XXXX-XXXX-XXXX
     */
    public static function generateKey()
    {
        do {
            $key = 'NCS-' . strtoupper(\Illuminate\Support\Str::random(4)) . '-' . 
                   strtoupper(\Illuminate\Support\Str::random(4)) . '-' . 
                   strtoupper(\Illuminate\Support\Str::random(4));
        } while (self::where('key', $key)->exists());

        return $key;
    }
}
