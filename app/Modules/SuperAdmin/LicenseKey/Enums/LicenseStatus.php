<?php

namespace App\Modules\SuperAdmin\LicenseKey\Enums;

enum LicenseStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif',
            self::EXPIRED => 'Kedaluwarsa',
            self::SUSPENDED => 'Ditangguhkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::EXPIRED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::SUSPENDED => 'bg-amber-100 text-amber-800 border-amber-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
