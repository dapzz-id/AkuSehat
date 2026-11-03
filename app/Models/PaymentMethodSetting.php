<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class PaymentMethodSetting extends Model
{
    use HasUuids;

    protected $table = 'payment_method_settings';

    protected $fillable = [
        'method_key',
        'name',
        'description',
        'is_active',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Dapatkan seluruh setting metode pembayaran terurut
     */
    public static function getAllSettings()
    {
        if (!Schema::hasTable('payment_method_settings')) {
            return collect(self::getDefaultCollection());
        }

        return self::orderBy('sort_order', 'asc')->get();
    }

    /**
     * Dapatkan hanya metode pembayaran yang aktif
     */
    public static function getActiveMethods()
    {
        if (!Schema::hasTable('payment_method_settings')) {
            return collect(self::getDefaultCollection())->where('is_active', true);
        }

        return self::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Cek apakah suatu metode pembayaran sedang aktif
     */
    public static function isMethodActive(string $methodKey): bool
    {
        $all = self::getAllSettings();
        $method = $all->firstWhere('method_key', $methodKey);
        return $method ? (bool) $method->is_active : false;
    }

    /**
     * Ambil konfigurasi spesifik suatu metode
     */
    public static function getMethodConfig(string $methodKey, ?string $field = null, mixed $default = null): mixed
    {
        $all = self::getAllSettings();
        $method = $all->firstWhere('method_key', $methodKey);
        if (!$method) {
            return $default;
        }

        $config = is_array($method->config) ? $method->config : [];

        // Fallback ke config services / env jika kosong di database
        if ($methodKey === 'midtrans') {
            $config['environment'] = $config['environment'] ?? config('services.midtrans.environment', 'sandbox');
            $config['server_key'] = $config['server_key'] ?? config('services.midtrans.server_key', '');
            $config['client_key'] = $config['client_key'] ?? config('services.midtrans.client_key', '');
            $config['merchant_id'] = $config['merchant_id'] ?? config('services.midtrans.merchant_id', '');
        }

        if ($field === null) {
            return $config;
        }

        return $config[$field] ?? $default;
    }

    /**
     * Fallback collection jika tabel belum termigrasi
     */
    protected static function getDefaultCollection(): array
    {
        return [
            (object)[
                'method_key' => 'manual_bca',
                'name' => 'Transfer Bank BCA (Manual)',
                'description' => 'Transfer langsung ke rekening Bank BCA dengan verifikasi bukti transfer',
                'is_active' => true,
                'config' => [
                    'bank_name' => 'BANK CENTRAL ASIA',
                    'account_number' => '842-171-3359',
                    'account_name' => 'Kadavi Raditya Alvino',
                    'whatsapp_number' => '+62 895-3831-07479',
                ],
                'sort_order' => 1,
            ],
            (object)[
                'method_key' => 'midtrans',
                'name' => 'Midtrans Payment Gateway (Snap)',
                'description' => 'Pembayaran instan online via QRIS, BCA/Mandiri/BRI Virtual Account, Gopay, ShopeePay, & Kartu',
                'is_active' => false,
                'config' => [
                    'environment' => config('services.midtrans.environment', 'sandbox'),
                    'server_key' => config('services.midtrans.server_key', ''),
                    'client_key' => config('services.midtrans.client_key', ''),
                    'merchant_id' => config('services.midtrans.merchant_id', ''),
                ],
                'sort_order' => 2,
            ],
            (object)[
                'method_key' => 'google_pay',
                'name' => 'Google Pay',
                'description' => 'Pembayaran instan melalui dompet digital Google Pay Web',
                'is_active' => false,
                'config' => [
                    'environment' => config('services.google_pay.environment', 'TEST'),
                    'merchant_id' => config('services.google_pay.merchant_id', ''),
                    'merchant_name' => config('services.google_pay.merchant_name', 'AkuSehat - Neura Cakrawira Solusi'),
                ],
                'sort_order' => 3,
            ],
        ];
    }
}

