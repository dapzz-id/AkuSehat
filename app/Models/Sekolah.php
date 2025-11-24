<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'alamat',
        'email',
        'telepon',
        'license_id_active',
        'website',
        'jenjang',
        'kota',
        'provinsi',
        'kode_pos'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id');
    }

    public function licenseKey()
    {
        return $this->belongsTo(LicenseKey::class, 'license_id_active');
    }

    public function sekolah()
    {
        return $this->hasMany(Sekolah::class, 'sekolah_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'sekolah_id');
    }

    public function getActiveLicense()
    {
        if (!$this->license_id_active) {
            return null;
        }

        return $this->licenseKey()->where('status', LicenseKey::STATUS_ACTIVE)->first();
    }
}
