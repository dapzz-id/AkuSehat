<?php

namespace App\Modules\Billing\Subscription\Enums;

enum BillingTransactionType: string
{
    case PERPANJANG = 'perpanjang';
    case PERPANJANG_LISENSI = 'perpanjang_lisensi';
    case TAMBAH_KUOTA = 'tambah_kuota';
    case QUOTA_TOPUP = 'quota_topup';
    case RENEWAL = 'renewal';

    public function label(): string
    {
        return match($this) {
            self::PERPANJANG, self::PERPANJANG_LISENSI, self::RENEWAL => 'Perpanjangan Lisensi',
            self::TAMBAH_KUOTA, self::QUOTA_TOPUP => 'Penambahan Kuota',
        };
    }

    public function isRenewal(): bool
    {
        return in_array($this, [self::PERPANJANG, self::PERPANJANG_LISENSI, self::RENEWAL]);
    }

    public function isQuotaTopup(): bool
    {
        return in_array($this, [self::TAMBAH_KUOTA, self::QUOTA_TOPUP]);
    }
}
