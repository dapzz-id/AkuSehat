<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataHaid extends Model
{
    use HasFactory;

    protected $table = 'data_haid';

    protected $fillable = [
        'id_user',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}