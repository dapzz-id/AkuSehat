<?php

namespace App\Modules\SuperAdmin\SystemConfig\Enums;

enum SoftwarePlatform: string
{
    case ANDROID = 'android';
    case IOS = 'ios';
    case WEB = 'web';

    public function label(): string
    {
        return match($this) {
            self::ANDROID => 'Android App',
            self::IOS => 'Apple iOS',
            self::WEB => 'Web Application',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
