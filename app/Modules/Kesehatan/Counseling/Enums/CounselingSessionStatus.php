<?php

namespace App\Modules\Kesehatan\Counseling\Enums;

enum CounselingSessionStatus: string
{
    case MENUNGGU = 'menunggu';
    case TERJADWAL = 'terjadwal';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::MENUNGGU => 'Menunggu Konfirmasi',
            self::TERJADWAL => 'Terjadwal',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::MENUNGGU => 'bg-amber-100 text-amber-800 border-amber-200',
            self::TERJADWAL => 'bg-blue-100 text-blue-800 border-blue-200',
            self::SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::DIBATALKAN => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::MENUNGGU => '#f59e0b',
            self::TERJADWAL => '#3b82f6',
            self::SELESAI => '#10b981',
            self::DIBATALKAN => '#f43f5e',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
