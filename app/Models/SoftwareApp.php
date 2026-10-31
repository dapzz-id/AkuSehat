<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwareStatus;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;

class SoftwareApp extends Model
{
    use HasUuids;

    protected $table = 'software_apps';

    protected $fillable = [
        'name',
        'version',
        'min_version',
        'description',
        'changelog',
        'developer',
        'link',
        'status',
        'icon',
        'platform',
        'is_mandatory',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_mandatory' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_android');
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_ios');
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_web');
            \Illuminate\Support\Facades\Cache::forever('global_system_version', (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6));
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_android');
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_ios');
            \Illuminate\Support\Facades\Cache::forget('latest_software_app_web');
            \Illuminate\Support\Facades\Cache::forever('global_system_version', (string) now()->timestamp . '_' . \Illuminate\Support\Str::random(6));
        });
    }

    public function isActive(): bool
    {
        return $this->status === SoftwareStatus::ACTIVE->value;
    }

    public function isWeb(): bool
    {
        return $this->platform === SoftwarePlatform::WEB->value;
    }

    public function isMobile(): bool
    {
        return in_array($this->platform, [SoftwarePlatform::ANDROID->value, SoftwarePlatform::IOS->value]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SoftwareStatus::ACTIVE->value);
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}
