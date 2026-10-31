<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Enums;

enum BmiStatus: string
{
    case KURUS = 'Kurus';
    case UNDERWEIGHT = 'Underweight';
    case NORMAL = 'Normal';
    case GEMUK = 'Gemuk';
    case OVERWEIGHT = 'Overweight';
    case OBESITAS = 'Obesitas';
    case OBESITAS_1 = 'Obesitas Level 1';
    case OBESITAS_2 = 'Obesitas Level 2';
    case OBESITAS_3 = 'Obesitas Level 3';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::KURUS, self::UNDERWEIGHT => 'bg-amber-100 text-amber-800 border-amber-200',
            self::NORMAL => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::GEMUK, self::OVERWEIGHT => 'bg-orange-100 text-orange-800 border-orange-200',
            self::OBESITAS, self::OBESITAS_1, self::OBESITAS_2, self::OBESITAS_3 => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
