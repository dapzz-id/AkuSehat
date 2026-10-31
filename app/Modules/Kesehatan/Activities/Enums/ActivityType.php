<?php

namespace App\Modules\Kesehatan\Activities\Enums;

enum ActivityType: string
{
    case RUN = 'run';
    case WALK = 'walk';
    case CYCLING = 'cycling';
    case WORKOUT = 'workout';

    public function label(): string
    {
        return match($this) {
            self::RUN => 'Lari / Jogging',
            self::WALK => 'Jalan Santai',
            self::CYCLING => 'Bersepeda',
            self::WORKOUT => 'Latihan / Workout',
        };
    }

    public function metValue(): float
    {
        return match($this) {
            self::RUN => 8.0,
            self::CYCLING => 6.0,
            self::WALK => 3.5,
            self::WORKOUT => 5.0,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
