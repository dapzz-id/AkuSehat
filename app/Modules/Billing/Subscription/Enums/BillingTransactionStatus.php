<?php

namespace App\Modules\Billing\Subscription\Enums;

enum BillingTransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case PAID = 'paid';
    case FAILED = 'failed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SUCCESS, self::PAID => 'Berhasil / Lunas',
            self::FAILED => 'Gagal',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::SUCCESS, self::PAID => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::FAILED, self::EXPIRED => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public function isSuccess(): bool
    {
        return in_array($this, [self::SUCCESS, self::PAID]);
    }
}
