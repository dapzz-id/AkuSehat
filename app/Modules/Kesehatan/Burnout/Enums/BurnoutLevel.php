<?php

namespace App\Modules\Kesehatan\Burnout\Enums;

enum BurnoutLevel: string
{
    case RENDAH = 'rendah';
    case SEDANG = 'sedang';
    case TINGGI = 'tinggi';

    public function label(): string
    {
        return match($this) {
            self::RENDAH => 'Rendah (Aman)',
            self::SEDANG => 'Sedang (Waspada)',
            self::TINGGI => 'Tinggi (Kritis)',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RENDAH => '#10b981',
            self::SEDANG => '#f59e0b',
            self::TINGGI => '#f43f5e',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::RENDAH => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::SEDANG => 'bg-amber-100 text-amber-800 border-amber-200',
            self::TINGGI => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
