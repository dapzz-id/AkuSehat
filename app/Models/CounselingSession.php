<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class CounselingSession extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'counseling_sessions';

    protected $fillable = [
        'instansi_id',
        'user_id',
        'consultant_id',
        'session_date',
        'topic',
        'ruangan',
        'notes',
        'hasil_konseling',
        'status',
        'is_confidential',
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'is_confidential' => 'boolean',
        'notes' => \App\Casts\TenantEncrypted::class,
        'hasil_konseling' => \App\Casts\TenantEncrypted::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }
}
