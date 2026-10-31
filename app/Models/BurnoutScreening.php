<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class BurnoutScreening extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $fillable = [
        'user_id',
        'instansi_id',
        'screening_date',
        'stress_score',
        'burnout_level',
        'workload_rating',
        'notes',
    ];

    protected $casts = [
        'screening_date' => 'date',
        'stress_score' => 'integer',
        'workload_rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
