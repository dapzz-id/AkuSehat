<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'tgl',
        'kelas',
        'jurusan',
        'count_kesehatan'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_kelas');
    }

    public function kesehatan()
    {
        return $this->hasMany(Kesehatan::class, 'id_kelas');
    }

     public function hb()
    {
        return $this->hasMany(Hb::class, 'id_kelas');
    }
}
