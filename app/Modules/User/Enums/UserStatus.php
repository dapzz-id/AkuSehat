<?php

namespace App\Modules\User\Enums;

enum UserStatus: string
{
    case AKTIF = 'aktif';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';
    case NONAKTIF = 'nonaktif';

    public function label(): string
    {
        return match($this) {
            self::AKTIF => 'Aktif',
            self::SUSPENDED => 'Ditangguhkan (Suspended)',
            self::BANNED => 'Diblokir (Banned)',
            self::NONAKTIF => 'Nonaktif',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::AKTIF => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::SUSPENDED => 'bg-amber-100 text-amber-800 border-amber-200',
            self::BANNED, self::NONAKTIF => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
