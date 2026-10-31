<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Enums;

enum BloodPressureStatus: string
{
    case HIPOTENSI = 'Hipotensi';
    case NORMAL = 'Normal';
    case ELEVASI = 'Elevasi';
    case PRAHIPERTENSI = 'Prahipertensi';
    case HIPERTENSI_1 = 'Hipertensi Derajat 1';
    case HIPERTENSI_2 = 'Hipertensi Derajat 2';
    case HIPERTENSI_TAHAP_1 = 'Hipertensi Tahap 1';
    case HIPERTENSI_TAHAP_2 = 'Hipertensi Tahap 2';
    case KRISIS = 'Krisis';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::HIPOTENSI => 'bg-blue-100 text-blue-800 border-blue-200',
            self::NORMAL => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::ELEVASI, self::PRAHIPERTENSI => 'bg-amber-100 text-amber-800 border-amber-200',
            self::HIPERTENSI_1, self::HIPERTENSI_TAHAP_1 => 'bg-orange-100 text-orange-800 border-orange-200',
            self::HIPERTENSI_2, self::HIPERTENSI_TAHAP_2, self::KRISIS => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
