<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PeminjamanPita extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_pita';

    protected $fillable = [
        'id_user',
        'tanggal_pinjam',
        'tanggal_kembali',
        'jumlah_pita',
        'verified',
        'status',
        'estimasi_selesai_haid',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'estimasi_selesai_haid' => 'date',
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function isTerlambat()
    {
        if ($this->status === 'dikembalikan') {
            return false;
        }

        return Carbon::now()->gt($this->estimasi_selesai_haid);
    }

    public function hariTerlambat()
    {
        if (!$this->isTerlambat()) {
            return 0;
        }

        return Carbon::parse($this->estimasi_selesai_haid)->diffInDays(Carbon::now());
    }
}