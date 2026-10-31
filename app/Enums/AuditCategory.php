<?php

namespace App\Enums;

enum AuditCategory: string
{
    case AUTH = 'auth';
    case BILLING = 'billing';
    case USER = 'user';
    case INSTANSI = 'instansi';
    case MAINTENANCE = 'maintenance';
    case MODULE = 'module';
    case SYSTEM = 'system';
    case SECURITY = 'security';
    case HEALTH = 'health';

    public function label(): string
    {
        return match($this) {
            self::AUTH => 'Autentikasi & Sesi',
            self::BILLING => 'Langganan & Transaksi',
            self::USER => 'Manajemen Pengguna',
            self::INSTANSI => 'Institusi / Instansi',
            self::MAINTENANCE => 'Pemeliharaan Sistem',
            self::MODULE => 'Hak Akses Modul',
            self::SYSTEM => 'Sistem & Server',
            self::SECURITY => 'Keamanan & Proteksi',
            self::HEALTH => 'Pemeriksaan Kesehatan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
