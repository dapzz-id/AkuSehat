<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class UserActivity extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'user_activities';

    protected $fillable = [
        'user_id',
        'instansi_id',
        'activity_type',
        'title',
        'distance_meters',
        'duration_seconds',
        'calories',
        'avg_pace',
        'max_speed',
        'steps',
        'route_coordinates',
        'notes',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'distance_meters' => 'float',
        'duration_seconds' => 'integer',
        'calories' => 'float',
        'max_speed' => 'float',
        'steps' => 'integer',
        'route_coordinates' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper to get distance formatted in km
     */
    public function getDistanceKmAttribute()
    {
        return round($this->distance_meters / 1000, 2);
    }

    /**
     * Helper to get formatted duration HH:MM:SS
     */
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
