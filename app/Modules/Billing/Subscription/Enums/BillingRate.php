<?php

namespace App\Modules\Billing\Subscription\Enums;

enum BillingRate: int
{
    case HARGA_PER_USER_TAHUN = 12000;

    /**
     * Tarif prorata per bulan per user
     */
    public function perBulan(): float
    {
        return $this->value / 12;
    }

    /**
     * Hitung subtotal untuk kuota tertentu
     */
    public function calculateSubtotal(int $quantity, int $months = 12): float
    {
        if ($months === 12) {
            return (float) ($quantity * $this->value);
        }

        return round($quantity * $this->perBulan() * $months);
    }

    /**
     * Hitung PPN 11% dari subtotal
     */
    public static function calculatePpn(float $subtotal, float $rate = 0.11): int
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
