<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class NutritionLog extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $fillable = [
        'user_id',
        'instansi_id',
        'log_date',
        'meal_type',
        'food_name',
        'calories',
        'is_balanced',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'calories' => 'integer',
        'is_balanced' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
