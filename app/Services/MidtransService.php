<?php

namespace App\Services;

use App\Models\PaymentMethodSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Dapatkan mode environment (sandbox / production)
     */
    public function getEnvironment(): string
    {
        $env = PaymentMethodSetting::getMethodConfig('midtrans', 'environment');
        return !empty($env) ? strtolower($env) : config('services.midtrans.environment', 'sandbox');
    }

    public function isProduction(): bool
    {
        return $this->getEnvironment() === 'production';
    }

    /**
     * Dapatkan Server Key
     */
    public function getServerKey(): string
    {
        $key = PaymentMethodSetting::getMethodConfig('midtrans', 'server_key');
        return !empty($key) ? trim($key) : config('services.midtrans.server_key', '');
    }

    /**
     * Dapatkan Client Key
     */
    public function getClientKey(): string
    {
        $key = PaymentMethodSetting::getMethodConfig('midtrans', 'client_key');
        return !empty($key) ? trim($key) : config('services.midtrans.client_key', '');
    }

    /**
     * URL API Snap Base
     */
    public function getSnapBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    /**
     * URL Script Snap.js untuk frontend
     */
    public function getSnapJsUrl(): string
    {
        return $this->getSnapBaseUrl() . '/snap/snap.js';
    }

    /**
     * URL API Midtrans Core Base
     */
    public function getApiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Buat Snap Token transaksi baru
     */
    public function createSnapTransaction(array $params): array
    {
        $serverKey = $this->getServerKey();
        if (empty($serverKey)) {
            return [
                'success' => false,
                'message' => 'Midtrans Server Key belum dikonfigurasi di pengaturan sistem SuperAdmin.',
            ];
        }

        try {
            $authHeader = 'Basic ' . base64_encode($serverKey . ':');
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->getSnapBaseUrl() . '/snap/v1/transactions', $params);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'token' => $body['token'] ?? null,
                    'redirect_url' => $body['redirect_url'] ?? null,
                    'raw' => $body,
                ];
            }

            Log::error('Midtrans createSnapTransaction error: ' . $response->body());
            $errJson = $response->json();
            $errMsg = is_array($errJson['error_messages'] ?? null) 
                ? implode(', ', $errJson['error_messages']) 
                : ($errJson['message'] ?? 'Gagal membuat sesi pembayaran Midtrans.');

            return [
                'success' => false,
                'message' => $errMsg,
                'raw' => $errJson,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans createSnapTransaction exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghubungi gateway Midtrans: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validasi Signature Webhook dari Midtrans
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $serverKey = $this->getServerKey();
        $receivedSignature = (string) ($payload['signature_key'] ?? '');

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($serverKey) || empty($receivedSignature)) {
            return false;
        }

        // Midtrans hash: SHA512(order_id + status_code + gross_amount + server_key)
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Cek status transaksi langsung ke server Midtrans (verifikasi ganda)
     */
    public function getTransactionStatus(string $orderId): array
    {
        $serverKey = $this->getServerKey();
        if (empty($serverKey)) {
            return ['success' => false, 'message' => 'Server Key kosong'];
        }

        try {
            $authHeader = 'Basic ' . base64_encode($serverKey . ':');
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept' => 'application/json',
            ])->get($this->getApiBaseUrl() . "/v2/{$orderId}/status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengambil status transaksi: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Evaluasi apakah status transaksi Midtrans adalah Berhasil / Lunas
     */
    public function isTransactionSettled(string $transactionStatus, ?string $fraudStatus = null): bool
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'accept';
        }

        return $transactionStatus === 'settlement';
    }
}
