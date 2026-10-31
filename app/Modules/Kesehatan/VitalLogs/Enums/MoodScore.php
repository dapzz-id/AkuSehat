<?php

namespace App\Modules\Kesehatan\VitalLogs\Enums;

enum MoodScore: int
{
    case VERY_BAD = 1;
    case BAD = 2;
    case NEUTRAL = 3;
    case GOOD = 4;
    case VERY_GOOD = 5;

    public function label(): string
    {
        return match($this) {
            self::VERY_BAD => 'Sangat Buruk',
            self::BAD => 'Buruk / Kurang Bersemangat',
            self::NEUTRAL => 'Biasa Saja',
            self::GOOD => 'Baik / Bersemangat',
            self::VERY_GOOD => 'Sangat Baik / Luar Biasa',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
