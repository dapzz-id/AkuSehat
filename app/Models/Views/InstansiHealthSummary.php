<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class InstansiHealthSummary extends Model
{
    use BelongsToTenant;

    protected $table = 'v_instansi_health_summary';
    protected $primaryKey = 'instansi_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
