<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Log;

/**
 * Bank-Grade Financial & Identity Data Vault Encryption Service.
 *
 * Menggunakan standar kriptografi perbankan (PCI-DSS / NIST SP 800-38D):
 * - Algoritma: AES-256-GCM (Authenticated Encryption with Associated Data / AEAD)
 * - Key Derivation: HKDF-SHA256 (RFC 5869)
 * - Key Separation: Menggunakan DATA_SECRET_KEY dari .env yang terpisah dari APP_KEY
 * - Nonce/IV: 96-bit (12-byte) CSPRNG per operasi enkripsi
 * - Integrity Tag: 128-bit (16-byte) GCM Authentication Tag anti-tampering
 */
class DataVaultEncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LENGTH = 12;
    private const TAG_LENGTH = 16;
    private const PREFIX = 'enc:vault:v1:';
    private const HKDF_INFO = 'AkuSehat-Financial-Vault-AES256GCM-v1';

    /**
     * Cek apakah DATA_SECRET_KEY khusus telah dikonfigurasi di .env
     */
    public function hasDedicatedSecretKey(): bool
    {
        $secret = trim((string) config('services.vault.secret_key', ''));
        return !empty($secret) && !str_contains($secret, 'xxxx');
    }

    /**
     * Dapatkan kunci master untuk Data Vault (terpisah dari APP_KEY).
     */
    protected function getPrimaryRawSecret(): string
    {
        $secret = trim((string) config('services.vault.secret_key', ''));
        if (!empty($secret)) {
            if (str_starts_with($secret, 'base64:')) {
                $decoded = base64_decode(substr($secret, 7), true);
                if ($decoded !== false && strlen($decoded) >= 16) {
                    return $decoded;
                }
            }
            return $secret;
        }

        return $this->getFallbackRawSecret();
    }

    /**
     * Fallback raw secret dari APP_KEY dengan domain separation jika DATA_SECRET_KEY belum diisi di .env.
     */
    protected function getFallbackRawSecret(): string
    {
        $key = (string) config('app.key', 'default-fallback-secret-key');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded !== false) {
                return $decoded;
            }
        }
        return $key;
    }

    /**
     * Turunkan kunci 256-bit (32-byte) menggunakan HKDF-SHA256.
     */
    protected function deriveKey(string $rawSecret, string $context = 'default'): string
    {
        return hash_hkdf('sha256', $rawSecret, 32, self::HKDF_INFO, $context);
    }

    /**
     * Enkripsi string menggunakan AES-256-GCM + AAD (Additional Authenticated Data).
     */
    public function encryptString(string $plaintext, string $context = 'payment_method_settings'): string
    {
        $key = $this->deriveKey($this->getPrimaryRawSecret(), $context);
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';
        $aad = 'context:' . $context;

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad,
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Gagal melakukan enkripsi AES-256-GCM pada DataVault.');
        }

        $envelope = [
            'v'   => 1,
            'alg' => self::CIPHER,
            'ctx' => $context,
            'iv'  => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ct'  => base64_encode($ciphertext),
        ];

        return self::PREFIX . base64_encode(json_encode($envelope));
    }

    /**
     * Dekripsi string AES-256-GCM dengan dukungan Dual-Key Rotation (Primary DATA_SECRET_KEY & Fallback).
     */
    public function decryptString(?string $payload, string $context = 'payment_method_settings'): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        if (!str_starts_with($payload, self::PREFIX)) {
            return $payload;
        }

        try {
            $encoded = substr($payload, strlen(self::PREFIX));
            $decodedJson = base64_decode($encoded, true);
            if ($decodedJson === false) {
                return null;
            }

            $envelope = json_decode($decodedJson, true);
            if (!is_array($envelope) || !isset($envelope['iv'], $envelope['tag'], $envelope['ct'])) {
                return null;
            }

            $iv = base64_decode($envelope['iv'], true);
            $tag = base64_decode($envelope['tag'], true);
            $ciphertext = base64_decode($envelope['ct'], true);
            $envContext = $envelope['ctx'] ?? $context;
            $aad = 'context:' . $envContext;

            // 1. Coba dekripsi dengan kunci utama (DATA_SECRET_KEY)
            $primaryKey = $this->deriveKey($this->getPrimaryRawSecret(), $envContext);
            $decrypted = openssl_decrypt(
                $ciphertext,
                self::CIPHER,
                $primaryKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                $aad
            );

            if ($decrypted !== false) {
                return $decrypted;
            }

            // 2. Fallback dekripsi jika data dienkripsi sebelum DATA_SECRET_KEY ditambahkan ke .env
            $fallbackKey = $this->deriveKey($this->getFallbackRawSecret(), $envContext);
            $decryptedFallback = openssl_decrypt(
                $ciphertext,
                self::CIPHER,
                $fallbackKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                $aad
            );

            return $decryptedFallback !== false ? $decryptedFallback : null;
        } catch (\Throwable $e) {
            Log::warning('DataVaultEncryptionService decrypt failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enkripsi array konfigurasi menjadi struktur JSON Vault yang aman disimpan di kolom JSON database.
     */
    public function encryptArray(array $data, string $context = 'payment_method_settings'): array
    {
        if (empty($data)) {
            return [];
        }

        $jsonPlain = json_encode($data, JSON_UNESCAPED_UNICODE);
        return [
            '_vault' => $this->encryptString($jsonPlain, $context),
        ];
    }

    /**
     * Dekripsi array konfigurasi dari struktur JSON Vault (mendukung transisi dari legacy plain JSON).
     */
    public function decryptArray(mixed $rawValue, string $context = 'payment_method_settings'): array
    {
        if (empty($rawValue)) {
            return [];
        }

        $decoded = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;
        if (!is_array($decoded)) {
            return [];
        }

        // Jika sudah terenkripsi dalam format Vault (_vault)
        if (isset($decoded['_vault']) && is_string($decoded['_vault'])) {
            $decryptedJson = $this->decryptString($decoded['_vault'], $context);
            if ($decryptedJson !== null) {
                $arr = json_decode($decryptedJson, true);
                return is_array($arr) ? $arr : [];
            }
            return [];
        }

        // Jika masih berupa legacy plain JSON (belum terenkripsi), kembalikan langsung
        return $decoded;
    }

    /**
     * Masking nomor rekening untuk tampilan aman (contoh: •••-•••-3359)
     */
    public static function maskAccountNumber(string $accountNumber): string
    {
        $clean = trim($accountNumber);
        if (strlen($clean) <= 4) {
            return '••••';
        }
        $last4 = substr($clean, -4);
        $prefix = substr($clean, 0, strlen($clean) - 4);
        $maskedPrefix = preg_replace('/[0-9A-Za-z]/', '•', $prefix);
        return $maskedPrefix . $last4;
    }

    /**
     * Masking nama pemilik rekening (contoh: K••••• R•••••• A•••••)
     */
    public static function maskAccountName(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $maskedWords = array_map(function (string $word) {
            $len = mb_strlen($word);
            if ($len <= 1) {
                return $word;
            }
            return mb_substr($word, 0, 1) . str_repeat('•', min($len - 1, 6));
        }, $words);

        return implode(' ', $maskedWords);
    }

    /**
     * Masking nomor WhatsApp (contoh: +62 •••-••••-•7479)
     */
    public static function maskPhoneNumber(string $phone): string
    {
        $clean = trim($phone);
        if (strlen($clean) <= 6) {
            return '••••••';
        }
        $first3 = substr($clean, 0, 3);
        $last4 = substr($clean, -4);
        $middle = substr($clean, 3, strlen($clean) - 7);
        $maskedMiddle = preg_replace('/[0-9]/', '•', $middle);
        return $first3 . $maskedMiddle . $last4;
    }
}
