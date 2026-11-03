<?php

namespace App\Casts;

use App\Services\Security\DataVaultEncryptionService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent Cast untuk mengenkripsi kolom JSON `config` pada PaymentMethodSetting
 * menggunakan AES-256-GCM + HKDF-SHA256 dengan kunci terpisah `DATA_SECRET_KEY`.
 */
class EncryptedVaultConfig implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $context = 'payment_method:' . ($attributes['method_key'] ?? 'default');
        return app(DataVaultEncryptionService::class)->decryptArray($value, $context);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        $arr = is_array($value) ? $value : [];
        if (empty($arr)) {
            return null;
        }

        $context = 'payment_method:' . ($attributes['method_key'] ?? $model->method_key ?? 'default');
        $vaultArray = app(DataVaultEncryptionService::class)->encryptArray($arr, $context);

        return json_encode($vaultArray, JSON_UNESCAPED_UNICODE);
    }
}

