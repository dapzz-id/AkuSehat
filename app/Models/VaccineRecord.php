<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class VaccineRecord extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $fillable = [
        'user_id',
        'instansi_id',
        'vaccine_name',
        'dose_number',
        'administered_date',
        'batch_number',
        'administered_by',
        'allergy_notes',
    ];

    protected $casts = [
        'dose_number' => 'integer',
        'administered_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
