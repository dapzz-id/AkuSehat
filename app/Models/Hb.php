<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hb extends Model
{
    use HasFactory;

    protected $table = 'hb';
    protected $primaryKey = 'id_hb';

    protected $fillable = [
        'id_user',
        'id_divisi',
        'tgl',
        'hb',
        'status',
        'pesan'
    ];

    protected $casts = [
        'tgl' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    public function statusHb()
    {
        $hb = floatval($this->hb);
        $jk = $this->user->jk;
        
        if ($jk === 'P') { // Perempuan
            if ($hb < 12) return 'Anemia';
            elseif ($hb >= 12 && $hb <= 15) return 'Normal';
            else return 'Tinggi';
        } else { // Laki-laki
            if ($hb < 13) return 'Anemia';
            elseif ($hb >= 13 && $hb <= 17) return 'Normal';
            else return 'Tinggi';
        }
    }
}