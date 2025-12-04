<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table = 'instansi';

    protected $fillable = [
        'nama_instansi',
        'alamat',
        'email',
        'telepon',
        'license_id_active',
        'website',
        'kota',
        'provinsi',
        'kode_pos'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'instansi_id');
    }

    public function licenseKey()
    {
        return $this->belongsTo(LicenseKey::class, 'license_id_active');
    }

    public function divisi()
    {
        return $this->hasMany(Divisi::class, 'instansi_id');
    }

    public function getActiveLicense()
    {
        if (!$this->license_id_active) {
            return null;
        }

        return $this->licenseKey()->where('status', LicenseKey::STATUS_ACTIVE)->first();
    }
}
