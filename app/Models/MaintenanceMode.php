<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceMode extends Model
{
    use HasFactory;

    protected $table = 'maintenance_mode';

    protected $fillable = [
        'is_web_maintenance',
        'is_mobile_maintenance',
    ];

    public static function isWebInMaintenance()
    {
        $mode = self::find(2); // ambil baris dengan id = 2 (untuk Web)
        return $mode ? (bool) $mode->is_web_maintenance : false;
    }

    public static function isMobileInMaintenance()
    {
        $mode = self::find(1); // ambil baris dengan id = 1 (untuk Mobile)
        return $mode ? (bool) $mode->is_mobile_maintenance : false;
    }

    protected $casts = [
        'is_web_maintenance' => 'boolean',
        'is_mobile_maintenance' => 'boolean',
    ];
}
