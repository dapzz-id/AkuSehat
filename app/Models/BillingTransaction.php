<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class BillingTransaction extends Model
{
    use HasUuids, BelongsToTenant;

    protected $table = 'billing_transactions';

    protected $fillable = [
        'instansi_id',
        'user_id',
        'order_id',
        'tipe_transaksi',
        'durasi_bulan',
        'tambah_kuota',
        'nominal',
        'payment_method',
        'payment_token',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'nominal' => 'float',
        'durasi_bulan' => 'integer',
        'tambah_kuota' => 'integer',
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
