<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kesehatan extends Model
{
    use HasFactory;

    protected $table = 'kesehatan';
    protected $primaryKey = 'id_kesehatan';

    protected $fillable = [
        'id_user',
        'tgl',
        'bb',
        'tb',
        'sistol',
        'diastol',
        'status_darah',
        'imt',
        'status',
        'pesan_imt',
        'pesan_tkd',
        'kondisi_telinga',
        'kondisi_gigi',
        'perilaku_beresiko',
        'gangguan_reproduksi'
    ];

    protected $casts = [
        'tgl' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function hitungIMT()
    {
        $bb = floatval($this->bb);
        $tb = floatval($this->tb) / 100; // convert to meter
        
        if ($tb > 0) {
            return round($bb / ($tb * $tb), 2);
        }
        return 0;
    }

    public function statusIMT()
    {
        $imt = $this->hitungIMT();
        
        if ($imt < 18.5) {
            return 'Kurus';
        } elseif ($imt >= 18.5 && $imt < 25) {
            return 'Normal';
        } elseif ($imt >= 25 && $imt < 30) {
            return 'Overweight';
        } else {
            return 'Obesitas';
        }
    }
}