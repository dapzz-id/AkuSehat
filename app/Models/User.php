<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'id_divisi',
        'instansi_id',
        'license_key_id',
        'tgl',
        'nis',
        'username',
        'password',
        'nama',
        'level',
        'jk',
        'nomor_induk',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function licenseKey()
    {
        return $this->belongsTo(LicenseKey::class, 'license_key_id');
    }

    public function isAdmin()
    {
        return $this->level === 'Admin';
    }

    public function isSuperAdmin()
    {
        return $this->level === 'SuperAdmin';
    }

    public function isHealthMonitor()
    {
        return $this->level === 'Health Monitor';
    }

    public function isHealthConsultant()
    {
        return $this->level === 'Health Consultant';
    }

    public function isMember()
    {
        return $this->level === 'Member';
    }

    public function isPerempuan()
    {
        return $this->jk === 'P';
    }
}
