<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PaymentMethodSetting;
use App\Modules\Billing\Subscription\Enums\PaymentMethod;
use App\Services\MidtransService;

class MidtransAndPaymentMethodsTest extends TestCase
{
    public function test_payment_method_enum_contains_midtrans(): void
    {
        $this->assertEquals('midtrans', PaymentMethod::MIDTRANS->value);
        $this->assertStringContainsString('Midtrans', PaymentMethod::MIDTRANS->label());
    }

    public function test_billing_transaction_type_enums(): void
    {
        $this->assertEquals('perpanjang_lisensi', \App\Modules\Billing\Subscription\Enums\BillingTransactionType::PERPANJANG_LISENSI->value);
        $this->assertEquals('tambah_kuota', \App\Modules\Billing\Subscription\Enums\BillingTransactionType::TAMBAH_KUOTA->value);
    }

    public function test_midtrans_service_urls_and_environment(): void
    {
        $service = new MidtransService();
        
        $snapUrl = $service->getSnapBaseUrl();
        $this->assertStringContainsString('midtrans.com', $snapUrl);

        $snapJsUrl = $service->getSnapJsUrl();
        $this->assertStringContainsString('snap.js', $snapJsUrl);
    }

    public function test_midtrans_signature_verification(): void
    {
        $service = $this->getMockBuilder(MidtransService::class)
            ->onlyMethods(['getServerKey'])
            ->getMock();

        $secretKey = 'SB-Mid-server-TEST-KEY-123';
        $service->method('getServerKey')->willReturn($secretKey);

        $orderId = 'MID-AKU-12345';
        $statusCode = '200';
        $grossAmount = '55500.00';

        $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $secretKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
        ];

        $this->assertTrue($service->verifySignature($payload));

        // Invalid signature
        $payload['signature_key'] = 'invalid-signature-hash';
        $this->assertFalse($service->verifySignature($payload));
    }

    public function test_midtrans_settlement_status_evaluation(): void
    {
        $service = new MidtransService();

        $this->assertTrue($service->isTransactionSettled('settlement'));
        $this->assertTrue($service->isTransactionSettled('capture', 'accept'));
        $this->assertFalse($service->isTransactionSettled('capture', 'challenge'));
        $this->assertFalse($service->isTransactionSettled('pending'));
        $this->assertFalse($service->isTransactionSettled('deny'));
        $this->assertFalse($service->isTransactionSettled('expire'));
        $this->assertFalse($service->isTransactionSettled('cancel'));
    }

    public function test_payment_method_setting_retrieval(): void
    {
        $settings = PaymentMethodSetting::getAllSettings();
        $this->assertNotEmpty($settings);

        $keys = $settings->pluck('method_key')->toArray();
        $this->assertContains('manual_bca', $keys);
        $this->assertContains('midtrans', $keys);
        $this->assertContains('google_pay', $keys);
    }

    public function test_bank_grade_data_vault_aes256_gcm_encryption_and_key_separation(): void
    {
        config(['services.vault.secret_key' => 'base64:' . base64_encode(random_bytes(32))]);
        $vault = new \App\Services\Security\DataVaultEncryptionService();

        $this->assertTrue($vault->hasDedicatedSecretKey());

        $bankData = [
            'bank_name' => 'BANK CENTRAL ASIA',
            'account_number' => '842-171-3359',
            'account_name' => 'Kadavi Raditya Alvino',
            'whatsapp_number' => '+62 895-3831-07479',
        ];

        $encryptedPayload = $vault->encryptArray($bankData, 'payment_method:manual_bca');
        $this->assertArrayHasKey('_vault', $encryptedPayload);
        $this->assertStringStartsWith('enc:vault:v1:', $encryptedPayload['_vault']);
        $this->assertStringNotContainsString('842-171-3359', json_encode($encryptedPayload));

        // Decrypt back with matching context
        $decrypted = $vault->decryptArray($encryptedPayload, 'payment_method:manual_bca');
        $this->assertEquals($bankData, $decrypted);

        // Masking tests
        $this->assertEquals('•••-•••-3359', \App\Services\Security\DataVaultEncryptionService::maskAccountNumber('842-171-3359'));
        $this->assertStringContainsString('K•••••', \App\Services\Security\DataVaultEncryptionService::maskAccountName('Kadavi Raditya Alvino'));
    }
}
