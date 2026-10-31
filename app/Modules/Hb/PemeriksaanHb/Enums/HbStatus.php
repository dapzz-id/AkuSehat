<?php

namespace App\Modules\Hb\PemeriksaanHb\Enums;

enum HbStatus: string
{
    case NORMAL = 'Normal';
    case ANEMIA = 'Anemia';
    case TINGGI = 'Tinggi';

    public function label(): string
    {
        return match($this) {
            self::NORMAL => 'Normal',
            self::ANEMIA => 'Anemia (Rendah)',
            self::TINGGI => 'Tinggi',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::NORMAL => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::ANEMIA => 'bg-rose-100 text-rose-800 border-rose-200',
            self::TINGGI => 'bg-amber-100 text-amber-800 border-amber-200',
        };
    }

    public function defaultMessage(): string
    {
        return match($this) {
            self::NORMAL => 'Kadar hemoglobin normal.',
            self::ANEMIA => 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.',
            self::TINGGI => 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.',
        };
    }
}
