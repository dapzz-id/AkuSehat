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
        'id_kelas',
        'tgl',
        'nis',
        'username',
        'password',
        'nama',
        'level',
        'jk'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
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

    public function isAdminPMR()
    {
        return $this->level === 'Admin PMR';
    }

    public function isGuruBK()
    {
        return $this->level === 'Guru BK';
    }

    public function isGuruOlahraga()
    {
        return $this->level === 'Guru Olahraga';
    }

    public function isSiswa()
    {
        return $this->level === 'Siswa';
    }

    public function isPerempuan()
    {
        return $this->jk === 'P';
    }
}
