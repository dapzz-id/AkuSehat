<?php

namespace App\Modules\User\Enums;

enum UserRole: string
{
    case MEMBER = 'Member';
    case ADMIN = 'Admin';
    case HEALTH_MONITOR = 'Health Monitor';
    case HEALTH_CONSULTANT = 'Health Consultant';
    case ADMIN_INSTANSI = 'Admin Instansi';
    case SUPERADMIN = 'SuperAdmin';

    public function label(): string
    {
        return match($this) {
            self::MEMBER => 'Member (Siswa / Mahasiswa / Karyawan)',
            self::ADMIN => 'Admin PMR / Koordinator Unit',
            self::HEALTH_MONITOR => 'Health Monitor (Guru Penjas / Petugas Medis / HSE)',
            self::HEALTH_CONSULTANT => 'Health Consultant (Guru BK / Konselor / Psikolog)',
            self::ADMIN_INSTANSI => 'Admin Instansi / Institusi',
            self::SUPERADMIN => 'Super Administrator Pusat',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
