<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class ErgonomicsLog extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $fillable = [
        'user_id',
        'instansi_id',
        'log_date',
        'sedentary_minutes',
        'breaks_taken',
        'posture_complaint',
        'score',
    ];

    protected $casts = [
        'log_date' => 'date',
        'sedentary_minutes' => 'integer',
        'breaks_taken' => 'integer',
        'score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
