<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class UserVitalLog extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'user_vital_logs';

    protected $fillable = [
        'user_id',
        'instansi_id',
        'log_date',
        'water_intake_ml',
        'sleep_hours',
        'mood_score',
        'systolic',
        'diastolic',
        'blood_sugar',
        'weight_kg',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'water_intake_ml' => 'integer',
        'sleep_hours' => 'float',
        'mood_score' => 'integer',
        'systolic' => 'integer',
        'diastolic' => 'integer',
        'blood_sugar' => 'float',
        'weight_kg' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
