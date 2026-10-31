<?php

namespace App\Modules\SuperAdmin\ModulePermission\Enums;

enum ModuleAccessStatus: string
{
    case ACTIVE = 'active';
    case MAINTENANCE = 'maintenance';
    case INVISIBLE = 'invisible';
    case TESTER_ONLY = 'tester_only';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif (Semua Pengguna)',
            self::MAINTENANCE => 'Pemeliharaan (Maintenance)',
            self::INVISIBLE => 'Disembunyikan (Invisible)',
            self::TESTER_ONLY => 'Khusus Akun Penguji (Tester Only)',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::ACTIVE => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::MAINTENANCE => 'bg-amber-100 text-amber-800 border-amber-200',
            self::INVISIBLE => 'bg-slate-100 text-slate-800 border-slate-200',
            self::TESTER_ONLY => 'bg-purple-100 text-purple-800 border-purple-200',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
