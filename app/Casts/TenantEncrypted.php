<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\Services\Security\TenantEncryptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

/**
 * Eloquent Cast untuk Enkripsi Otomatis Berbasis Kunci Instansi (Tenant-Isolated Encryption).
 * Hanya pengguna atau proses yang memiliki konteks instansi terkait yang dapat mendekripsi.
 */
class TenantEncrypted implements CastsAttributes
{
    protected function getService(): TenantEncryptionService
    {
        return app(TenantEncryptionService::class);
    }

    protected function resolveTenantId(Model $model, array $attributes): ?string
    {
        $tenantId = $model->instansi_id ?? $attributes['instansi_id'] ?? null;
        if (!$tenantId && Auth::check()) {
            $tenantId = Auth::user()?->instansi_id;
        }
        return $tenantId ? (string) $tenantId : null;
    }

    /**
     * Transform the attribute from the underlying model values (Decrypt).
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $tenantId = $this->resolveTenantId($model, $attributes);
        if (!$tenantId) {
            // Fallback ke standard safe decrypt jika model tidak memiliki instansi_id
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $this->getService()->decryptForTenant($value, $tenantId);
    }

    /**
     * Transform the attribute to its underlying model values (Encrypt).
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Hindari enkripsi ganda jika nilai sudah berformat enc:v1:
        if (is_string($value) && str_starts_with($value, 'enc:v1:')) {
            return $value;
        }

        $tenantId = $this->resolveTenantId($model, $attributes);
        if (!$tenantId) {
            try {
                return Crypt::encryptString($value);
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $this->getService()->encryptForTenant((string) $value, $tenantId);
    }
}
