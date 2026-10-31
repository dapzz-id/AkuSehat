<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class DivisiDashboardStat extends Model
{
    use BelongsToTenant;

    protected $table = 'v_divisi_dashboard_stats';
    protected $primaryKey = 'divisi_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Supaya kompatibel dengan view blade yang memanggil ->id
    public function getIdAttribute()
    {
        return $this->divisi_id;
    }
}
