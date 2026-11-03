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
        'config' => \App\Casts\EncryptedVaultConfig::class,
        'sort_order' => 'integer',
    ];

    /**
     * Cek apakah kolom config di database sudah terenkripsi dalam format AES-256-GCM Vault
     */
    public function isEncryptedInDatabase(): bool
    {
        $raw = $this->getRawOriginal('config');
        if (empty($raw)) {
            return false;
        }
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        return is_array($decoded) && isset($decoded['_vault']) && str_starts_with((string) $decoded['_vault'], 'enc:vault:v1:');
    }

    /**
     * Dapatkan seluruh setting metode pembayaran terurut (otomatis mengenkripsi data legacy plaintext)
     */
    public static function getAllSettings()
    {
        if (!Schema::hasTable('payment_method_settings')) {
            return collect(self::getDefaultCollection());
        }

        $items = self::orderBy('sort_order', 'asc')->get();

        foreach ($items as $item) {
            if ($item->method_key === 'manual_bca' && !$item->isEncryptedInDatabase() && !empty($item->config)) {
                $item->config = $item->config;
                $item->saveQuietly();
            } elseif (in_array($item->method_key, ['midtrans', 'google_pay'], true) && $item->getRawOriginal('config') !== null) {
                $item->config = null;
                $item->saveQuietly();
            }
        }

        return $items;
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
        $normalizedKey = ($methodKey === 'manual_transfer') ? 'manual_bca' : $methodKey;
        $method = $all->firstWhere('method_key', $normalizedKey) ?? $all->firstWhere('method_key', $methodKey);
        return $method ? (bool) $method->is_active : false;
    }

    /**
     * Dapatkan setting spesifik berdasarkan method_key
     */
    public static function getSetting(string $methodKey): ?self
    {
        $normalizedKey = ($methodKey === 'manual_transfer') ? 'manual_bca' : $methodKey;

        if (!Schema::hasTable('payment_method_settings')) {
            $defaults = collect(self::getDefaultCollection());
            $found = $defaults->firstWhere('method_key', $normalizedKey) ?? $defaults->firstWhere('method_key', $methodKey);
            if ($found) {
                $model = new self();
                $model->method_key = $found->method_key;
                $model->name = $found->name;
                $model->description = $found->description;
                $model->is_active = $found->is_active;
                $model->config = $found->config;
                return $model;
            }
            return null;
        }

        return self::where('method_key', $normalizedKey)->first() ?? self::where('method_key', $methodKey)->first();
    }

    /**
     * Ambil konfigurasi spesifik suatu metode
     */
    public static function getMethodConfig(string $methodKey, ?string $field = null, mixed $default = null): mixed
    {
        // Kredensial sensitif Payment Gateway (Midtrans & Google Pay) diambil langsung dari .env (config/services.php)
        if ($methodKey === 'midtrans') {
            $config = [
                'environment' => strtolower((string) config('services.midtrans.environment', 'sandbox')),
                'server_key' => trim((string) config('services.midtrans.server_key', '')),
                'client_key' => trim((string) config('services.midtrans.client_key', '')),
                'merchant_id' => trim((string) config('services.midtrans.merchant_id', '')),
            ];

            return $field === null ? $config : ($config[$field] ?? $default);
        }

        if ($methodKey === 'google_pay') {
            $config = [
                'environment' => strtoupper((string) config('services.google_pay.environment', 'TEST')),
                'merchant_id' => trim((string) config('services.google_pay.merchant_id', '')),
                'merchant_name' => trim((string) config('services.google_pay.merchant_name', 'AkuSehat Portal')),
            ];

            return $field === null ? $config : ($config[$field] ?? $default);
        }

        $all = self::getAllSettings();
        $method = $all->firstWhere('method_key', $methodKey);
        if (!$method) {
            return $default;
        }

        $config = is_array($method->config) ? $method->config : [];

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

