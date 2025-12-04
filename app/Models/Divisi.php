<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'instansi_id',
        'tgl',
        'divisi_name',
        'color_cover',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_divisi');
    }

     public function hb()
    {
        return $this->hasMany(Hb::class, 'id_divisi');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function kesehatan()
    {
        return $this->hasManyThrough(
            Kesehatan::class,
            User::class,
            'id_divisi', // foreign key di users
            'id_user',   // foreign key di kesehatan
            'id',        // local key Divisi
            'id'         // local key User
        );
    }
}
