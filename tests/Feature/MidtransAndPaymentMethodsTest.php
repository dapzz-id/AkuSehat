<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PaymentMethodSetting;
use App\Modules\Billing\Subscription\Enums\PaymentMethod;
use App\Services\MidtransService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MidtransAndPaymentMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('instansi')) {
            Schema::create('instansi', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama_instansi')->nullable();
                $table->string('jenis_instansi')->nullable();
                $table->string('tipe_instansi')->nullable();
                $table->string('invite_code')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->string('nama')->nullable();
                $table->string('name')->nullable();
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('level')->nullable();
                $table->string('no_telp')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('billing_transactions')) {
            Schema::create('billing_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->uuid('user_id')->nullable();
                $table->string('order_id')->nullable();
                $table->string('tipe_transaksi')->nullable();
                $table->integer('durasi_bulan')->default(1);
                $table->integer('tambah_kuota')->default(0);
                $table->decimal('nominal', 14, 2)->default(0);
                $table->string('payment_method')->nullable();
                $table->text('payment_token')->nullable();
                $table->string('status')->default('pending');
                $table->json('payload')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('license_keys')) {
            Schema::create('license_keys', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->nullable();
                $table->string('key')->unique();
                $table->integer('kuota_pengguna')->default(8);
                $table->string('status')->default('active');
                $table->date('tanggal_berakhir');
                $table->timestamps();
            });
        }
    }
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

    public function test_switch_payment_method_and_cancel_transaction_workflow(): void
    {
        $instansi = \App\Models\Instansi::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama_instansi' => 'SMK Switch Method Test',
            'jenis_instansi' => 'Sekolah',
        ]);

        $admin = \App\Models\User::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama' => 'Admin Switch Test',
            'name' => 'Admin Switch Test',
            'username' => 'adminswitch' . rand(1000, 9999),
            'email' => 'admin.switch.' . rand(1000, 9999) . '@test.com',
            'password' => bcrypt('password'),
            'level' => 'Admin Instansi',
            'instansi_id' => $instansi->id,
        ]);

        $billingService = app(\App\Modules\Billing\Subscription\Services\BillingService::class);

        // 1. Create a pending Midtrans transaction
        $trx = \App\Models\BillingTransaction::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'instansi_id' => $instansi->id,
            'user_id' => $admin->id,
            'order_id' => 'MID-TEST-' . time(),
            'tipe_transaksi' => 'perpanjang_lisensi',
            'durasi_bulan' => 1,
            'nominal' => 55500,
            'payment_method' => 'midtrans',
            'payment_token' => 'snap-test-token-123',
            'status' => 'pending',
            'payload' => ['deskripsi_item' => 'Perpanjangan Lisensi 1 Bulan'],
        ]);

        // 2. Switch from Midtrans to manual BCA
        $switchResult = $billingService->switchPaymentMethod($admin, $trx->id, 'manual_transfer');
        $this->assertEquals(200, $switchResult['status_code']);
        $this->assertTrue($switchResult['response']['success']);
        $this->assertStringStartsWith('BCA-AKU-', $switchResult['response']['order_id']);

        $trx->refresh();
        $this->assertEquals('manual_transfer', $trx->payment_method);
        $this->assertStringStartsWith('BCA-AKU-', $trx->order_id);
        $this->assertNull($trx->payment_token);

        // 3. Cancel the transaction
        $cancelResult = $billingService->cancelBillingTransaction($admin, $trx->id, 'Salah pilih paket');
        $this->assertEquals(200, $cancelResult['status_code']);
        $this->assertTrue($cancelResult['response']['success']);

        $trx->refresh();
        $this->assertEquals('cancelled', $trx->status);

        // 4. Cannot switch or cancel an already cancelled transaction
        $cannotSwitch = $billingService->switchPaymentMethod($admin, $trx->id, 'manual_transfer');
        $this->assertEquals(400, $cannotSwitch['status_code']);

        // 5. Tenant isolation: Another user cannot cancel or switch
        $otherInstansi = \App\Models\Instansi::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama_instansi' => 'Instansi Lain',
            'jenis_instansi' => 'Sekolah',
        ]);
        $otherAdmin = \App\Models\User::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama' => 'Admin Lain',
            'name' => 'Admin Lain',
            'username' => 'adminlain' . rand(1000, 9999),
            'email' => 'admin.lain.' . rand(1000, 9999) . '@test.com',
            'password' => bcrypt('password'),
            'level' => 'Admin Instansi',
            'instansi_id' => $otherInstansi->id,
        ]);

        $pendingTrx = \App\Models\BillingTransaction::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'instansi_id' => $instansi->id,
            'user_id' => $admin->id,
            'order_id' => 'BCA-TEST-' . time(),
            'tipe_transaksi' => 'perpanjang_lisensi',
            'durasi_bulan' => 1,
            'nominal' => 55500,
            'payment_method' => 'manual_transfer',
            'status' => 'pending',
            'payload' => [],
        ]);

        $unauthorizedCancel = $billingService->cancelBillingTransaction($otherAdmin, $pendingTrx->id);
        $this->assertEquals(403, $unauthorizedCancel['status_code']);
    }

    public function test_expired_license_middleware_allows_admin_billing_and_users_only(): void
    {
        $instansi = \App\Models\Instansi::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama_instansi' => 'SMK Expired Test',
            'jenis_instansi' => 'Sekolah',
        ]);

        $admin = \App\Models\User::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama' => 'Admin Instansi',
            'name' => 'Admin Instansi',
            'username' => 'admin_exp_' . rand(1000, 9999),
            'email' => 'admin.exp.' . rand(1000, 9999) . '@test.com',
            'password' => bcrypt('password'),
            'level' => 'Admin Instansi',
            'instansi_id' => $instansi->id,
        ]);

        // License is expired
        \App\Models\LicenseKey::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'instansi_id' => $instansi->id,
            'key' => 'EXP-KEY-' . rand(1000, 9999),
            'kuota_pengguna' => 8,
            'status' => 'expired',
            'tanggal_berakhir' => now()->subDay()->toDateString(),
        ]);

        $middleware = new \App\Http\Middleware\CheckLicenseActive();

        // 1. Admin accessing billing route -> Allowed ($next called)
        $reqBilling = \Illuminate\Http\Request::create('/admin/billing', 'GET');
        $reqBilling->setUserResolver(fn() => $admin);
        $resBilling = $middleware->handle($reqBilling, fn() => response('allowed_billing'));
        $this->assertEquals('allowed_billing', $resBilling->getContent());

        // 2. Admin accessing users route -> Allowed ($next called)
        $reqUsers = \Illuminate\Http\Request::create('/admin/users', 'GET');
        $reqUsers->setUserResolver(fn() => $admin);
        $resUsers = $middleware->handle($reqUsers, fn() => response('allowed_users'));
        $this->assertEquals('allowed_users', $resUsers->getContent());

        // 3. Admin accessing other route (e.g. admin/kesehatan) -> Redirected to billing with warning
        $reqOther = \Illuminate\Http\Request::create('/admin/kesehatan', 'GET');
        $reqOther->setUserResolver(fn() => $admin);
        $resOther = $middleware->handle($reqOther, fn() => response('allowed_other'));
        $this->assertTrue($resOther->isRedirect(route('admin.billing.index')));

        // 4. Member accessing -> 403 Forbidden
        $member = \App\Models\User::create([
            'id' => (string) \Illuminate\Support\Str::uuid7(),
            'nama' => 'Siswa Member',
            'name' => 'Siswa Member',
            'username' => 'member_exp_' . rand(1000, 9999),
            'email' => 'member.exp.' . rand(1000, 9999) . '@test.com',
            'password' => bcrypt('password'),
            'level' => 'Member',
            'instansi_id' => $instansi->id,
        ]);
        $reqMember = \Illuminate\Http\Request::create('/admin/kesehatan', 'GET');
        $reqMember->headers->set('Accept', 'application/json');
        $reqMember->setUserResolver(fn() => $member);
        $resMember = $middleware->handle($reqMember, fn() => response('allowed_member'));
        $this->assertEquals(403, $resMember->getStatusCode());
    }
}
