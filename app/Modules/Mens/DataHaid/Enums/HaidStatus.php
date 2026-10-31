<?php

namespace App\Modules\Mens\DataHaid\Enums;

enum HaidStatus: string
{
    case BERLANGSUNG = 'berlangsung';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match($this) {
            self::BERLANGSUNG => 'Sedang Berlangsung',
            self::SELESAI => 'Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::BERLANGSUNG => 'bg-amber-100 text-amber-800 border-amber-200',
            self::SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
