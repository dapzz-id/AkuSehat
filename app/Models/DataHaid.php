<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class DataHaid extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'data_haid';

    protected $fillable = [
        'id_user',
        'instansi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'catatan' => \App\Casts\TenantEncrypted::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}