<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Eloquent Cast untuk Enkripsi Otomatis Atribut Sensitif (AES-256).
 * Dilengkapi proteksi Fail-Safe: tidak akan pernah melempar DecryptException
 * jika membaca data lama (legacy plaintext) atau saat enkripsi gagal.
 */
class SafeEncrypted implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values (Decrypt).
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException|\Throwable $e) {
            // Data legacy (belum dienkripsi) atau format invalid, kembalikan teks aslinya tanpa error
            return $value;
        }
    }

    /**
     * Transform the attribute to its underlying model values (Encrypt).
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::encryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
