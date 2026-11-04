<?php

namespace App\Modules\Billing\Subscription\Enums;

enum BillingRate: int
{
    case HARGA_PER_USER_BULAN = 10000;
    case HARGA_PER_USER_TAHUN = 120000;

    /**
     * Tarif per bulan per user (Rp 10.000 / pengguna / bulan)
     */
    public function perBulan(): float
    {
        return match ($this) {
            self::HARGA_PER_USER_BULAN => (float) $this->value,
            self::HARGA_PER_USER_TAHUN => $this->value / 12,
        };
    }

    /**
     * Hitung subtotal untuk kuota tertentu (default 1 bulan @ Rp 10.000/pengguna/bulan)
     */
    public function calculateSubtotal(int $quantity, int $months = 1): float
    {
        return round($quantity * $this->perBulan() * $months);
    }

    /**
     * Hitung PPN dari subtotal (Default 0.0 / Bebas PPN untuk entitas Non-PKP / UMKM)
     */
    public static function calculatePpn(float $subtotal, float $rate = 0.0): int
    {
        return (int) round($subtotal * $rate);
    }

    /**
     * Format harga ke Rupiah
     */
    public function formatted(): string
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }
}
