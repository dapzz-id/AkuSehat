<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MaintenanceMode extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'maintenance_mode';

    protected $fillable = [
        'is_web_maintenance',
        'is_mobile_maintenance',
    ];

    protected static ?self $runtimeCache = null;

    protected static function booted(): void
    {
        static::saved(function () {
            self::$runtimeCache = null;
            \Illuminate\Support\Facades\Cache::forget('maintenance_mode_record');
            \Illuminate\Support\Facades\Cache::forever('global_system_version', (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6));
        });

        static::deleted(function () {
            self::$runtimeCache = null;
            \Illuminate\Support\Facades\Cache::forget('maintenance_mode_record');
            \Illuminate\Support\Facades\Cache::forever('global_system_version', (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6));
        });
    }

    public static function getCached(): ?self
    {
        if (self::$runtimeCache !== null) {
            return self::$runtimeCache;
        }

        try {
            self::$runtimeCache = \Illuminate\Support\Facades\Cache::remember('maintenance_mode_record', 60, function () {
                return self::first();
            });
            return self::$runtimeCache;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function isWebInMaintenance(): bool
    {
        $mode = self::getCached();
        return $mode ? (bool) $mode->is_web_maintenance : false;
    }

    public static function isMobileInMaintenance(): bool
    {
        $mode = self::getCached();
        return $mode ? (bool) $mode->is_mobile_maintenance : false;
    }

    protected $casts = [
        'is_web_maintenance' => 'boolean',
        'is_mobile_maintenance' => 'boolean',
    ];
}
