<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Log;

/**
 * Service Enkripsi Data Sensitif Multi-Tenant & Per-User.
 * Menggunakan arsitektur Envelope Encryption (AES-256-GCM authenticated encryption)
 * dengan derivasi kunci kriptografis HKDF-SHA256 terisolasi per instansi/user.
 */
class TenantEncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const TAG_LENGTH = 16;
    private const IV_LENGTH = 12;

    /**
     * Dapatkan master application key.
     */
    protected function getMasterKey(): string
    {
        $key = config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        return $key;
    }

    /**
     * Turunkan kunci kriptografis spesifik untuk Instansi tertentu (HKDF-SHA256).
     * Instansi A dan Instansi B memiliki kunci enkripsi yang berbeda total.
     */
    public function deriveTenantKey(string $tenantId): string
    {
        return hash_hkdf('sha256', $this->getMasterKey(), 32, 'AkuSehat-Tenant-Key-v1', $tenantId);
    }

    /**
     * Turunkan kunci kriptografis spesifik untuk User tertentu (HKDF-SHA256).
     */
    public function deriveUserKey(string $userId): string
    {
        return hash_hkdf('sha256', $this->getMasterKey(), 32, 'AkuSehat-User-Key-v1', $userId);
    }

    /**
     * Enkripsi data teks menggunakan Kunci Instansi (AES-256-GCM).
     */
    public function encryptForTenant(string $plaintext, string $tenantId): string
    {
        $key = $this->deriveTenantKey($tenantId);
        return $this->encryptWithKey($plaintext, $key, ['tenant_id' => $tenantId]);
    }

    /**
     * Dekripsi data teks menggunakan Kunci Instansi.
     * Pastikan tidak error: jika data berupa legacy plain text atau kunci berbeda, kembalikan aman.
     */
    public function decryptForTenant(?string $payload, string $tenantId): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        $key = $this->deriveTenantKey($tenantId);
        return $this->decryptWithKey($payload, $key);
    }

    /**
     * Enkripsi data teks menggunakan Kunci User.
     */
    public function encryptForUser(string $plaintext, string $userId): string
    {
        $key = $this->deriveUserKey($userId);
        return $this->encryptWithKey($plaintext, $key, ['user_id' => $userId]);
    }

    /**
     * Dekripsi data teks menggunakan Kunci User.
     */
    public function decryptForUser(?string $payload, string $userId): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        $key = $this->deriveUserKey($userId);
        return $this->decryptWithKey($payload, $key);
    }

    /**
     * Engine enkripsi internal menggunakan AES-256-GCM.
     */
    protected function encryptWithKey(string $plaintext, string $key, array $metadata = []): string
    {
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        $envelope = [
            'v'    => 1,
            'alg'  => self::CIPHER,
            'iv'   => base64_encode($iv),
            'tag'  => base64_encode($tag),
            'data' => base64_encode($ciphertext),
        ];

        return 'enc:v1:' . base64_encode(json_encode($envelope));
    }

    /**
     * Engine dekripsi internal fail-safe (tidak akan pernah melempar exception crash).
     */
    protected function decryptWithKey(string $payload, string $key): ?string
    {
        // Cek format prefix envelope enkripsi
        if (!str_starts_with($payload, 'enc:v1:')) {
            // Data legacy (belum terenkripsi), return langsung tanpa error
            return $payload;
        }

        try {
            $rawJson = base64_decode(substr($payload, 7));
            $envelope = json_decode($rawJson, true);

            if (!is_array($envelope) || !isset($envelope['iv'], $envelope['tag'], $envelope['data'])) {
                return $payload;
            }

            $iv = base64_decode($envelope['iv']);
            $tag = base64_decode($envelope['tag']);
            $ciphertext = base64_decode($envelope['data']);

            $decrypted = openssl_decrypt(
                $ciphertext,
                self::CIPHER,
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($decrypted === false) {
                Log::warning('TenantEncryptionService: Gagal mendekripsi data (Kunci instansi tidak cocok atau data rusak).');
                return null;
            }

            return $decrypted;
        } catch (\Throwable $e) {
            Log::error('TenantEncryptionService Decrypt Error: ' . $e->getMessage());
            return $payload;
        }
    }

    /**
     * Generate pasangan kunci Asymmetric (Public / Private Key RSA 2048)
     * Untuk skenario End-to-End Encryption antar User & Konselor/Dokter.
     */
    public function generateAsymmetricKeyPair(): array
    {
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $details = openssl_pkey_get_details($res);
        $publicKey = $details['key'];

        return [
            'public_key'  => $publicKey,
            'private_key' => $privateKey,
        ];
    }
}
