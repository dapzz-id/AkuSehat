<?php

namespace App\Modules\SuperAdmin\MaintenanceMode\Enums;

enum MaintenancePlatform: string
{
    case WEB = 'web';
    case MOBILE = 'mobile';
    case BOTH = 'both';

    public function label(): string
    {
        return match($this) {
            self::WEB => 'Web Application (Website)',
            self::MOBILE => 'Mobile Application (Android & iOS)',
            self::BOTH => 'Semua Platform (Web & Mobile)',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
