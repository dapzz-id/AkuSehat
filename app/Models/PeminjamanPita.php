<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class PeminjamanPita extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'peminjaman_pita';

    protected $fillable = [
        'id_user',
        'instansi_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'jumlah_pita',
        'verified',
        'status',
        'estimasi_selesai_haid',
        'keterangan',
        'denda_nominal',
        'denda_status',
        'sanksi_catatan',
        'health_flag',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'estimasi_selesai_haid' => 'date',
        'verified' => 'boolean',
        'denda_nominal' => 'integer',
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

        if ($this->status === 'terlambat') {
            return true;
        }

        return Carbon::now()->startOfDay()->gt(Carbon::parse($this->estimasi_selesai_haid)->startOfDay());
    }

    public function hariTerlambat()
    {
        if (!$this->isTerlambat()) {
            return 0;
        }

        return (int) Carbon::parse($this->estimasi_selesai_haid)->startOfDay()->diffInDays(Carbon::now()->startOfDay());
    }

    /**
     * Hitung denda otomatis (default Rp 1.000 / hari keterlambatan)
     */
    public function hitungDenda($tarifPerHari = 1000)
    {
        $days = $this->hariTerlambat();
        return $days > 0 ? $days * $tarifPerHari : 0;
    }

    /**
     * Evaluasi apakah durasi haid / peminjaman melebihi batas wajar 7 hari (indikasi gangguan reproduksi)
     */
    public function isIndikasiGangguanReproduksi()
    {
        $days = $this->hariTerlambat();
        return $days >= 1 || $this->health_flag === 'perhatian_khusus' || $this->health_flag === 'rujuk_konseling';
    }
}