<?php

namespace App\Modules\SuperAdmin\SystemConfig\Enums;

enum SoftwareStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif (Rilis Publik)',
            self::INACTIVE => 'Nonaktif / Diarsipkan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
