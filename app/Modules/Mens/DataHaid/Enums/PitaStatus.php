<?php

namespace App\Modules\Mens\DataHaid\Enums;

enum PitaStatus: string
{
    case DIPINJAM = 'dipinjam';
    case KEMBALI = 'kembali';
    case DIKEMBALIKAN = 'dikembalikan';
    case TERLAMBAT = 'terlambat';
    case MENUNGGU = 'menunggu';
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public function label(): string
    {
        return match($this) {
            self::DIPINJAM => 'Sedang Dipinjam',
            self::KEMBALI, self::DIKEMBALIKAN => 'Sudah Dikembalikan',
            self::TERLAMBAT => 'Terlambat Dikembalikan',
            self::MENUNGGU, self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DIPINJAM => 'bg-blue-100 text-blue-800 border-blue-200',
            self::KEMBALI, self::DIKEMBALIKAN => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::TERLAMBAT => 'bg-rose-100 text-rose-800 border-rose-200',
            self::MENUNGGU, self::MENUNGGU_VERIFIKASI => 'bg-amber-100 text-amber-800 border-amber-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
