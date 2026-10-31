<?php

namespace App\Modules\Kesehatan\Nutrition\Enums;

enum MealType: string
{
    case SARAPAN = 'sarapan';
    case MAKAN_SIANG = 'makan_siang';
    case MAKAN_MALAM = 'makan_malam';
    case SNACK = 'snack';

    public function label(): string
    {
        return match($this) {
            self::SARAPAN => 'Sarapan Pagi',
            self::MAKAN_SIANG => 'Makan Siang',
            self::MAKAN_MALAM => 'Makan Malam',
            self::SNACK => 'Camilan / Snack',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
