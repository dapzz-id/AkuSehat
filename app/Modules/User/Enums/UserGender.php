<?php

namespace App\Modules\User\Enums;

enum UserGender: string
{
    case LAKI_LAKI = 'L';
    case PEREMPUAN = 'P';

    public function label(): string
    {
        return match($this) {
            self::LAKI_LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
