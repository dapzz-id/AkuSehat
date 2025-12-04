<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    protected $table = 'license_keys';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';

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

    public function licenseKey()
    {
        return $this->hasMany(Instansi::class, 'license_id_active');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'license_key_id');
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && $this->tanggal_berakhir >= now();
    }

    /**
     * Cek apakah license sudah expired
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED || \Carbon\Carbon::now()->gt($this->tanggal_berakhir);
    }

    /**
     * Get sisa hari sebelum expired
     */
    public function getDaysRemaining()
    {
        if ($this->isExpired()) {
            return 0;
        }
        return \Carbon\Carbon::now()->diffInDays($this->tanggal_berakhir);
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
            if ($licenseKey->tanggal_berakhir < now()) {
                $licenseKey->status = self::STATUS_EXPIRED;
            }
        });
    }

    /**
     * Generate unique license key dengan format RDVZ-XXXX-XXXX-XXXX
     */
    public static function generateKey()
    {
        do {
            $key = 'RDVZ-' . strtoupper(\Illuminate\Support\Str::random(4)) . '-' . 
                   strtoupper(\Illuminate\Support\Str::random(4)) . '-' . 
                   strtoupper(\Illuminate\Support\Str::random(4));
        } while (self::where('key', $key)->exists());

        return $key;
    }
}
