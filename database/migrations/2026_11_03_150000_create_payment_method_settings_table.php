<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('payment_method_settings')) {
            Schema::create('payment_method_settings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('method_key', 50)->unique();
                $table->string('name', 100);
                $table->string('description', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('config')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });

            // Seed default settings
            $defaults = [
                [
                    'id' => (string) Str::uuid(),
                    'method_key' => 'manual_bca',
                    'name' => 'Transfer Bank BCA (Manual)',
                    'description' => 'Transfer langsung ke rekening Bank BCA dengan verifikasi bukti transfer',
                    'is_active' => true,
                    'config' => json_encode([
                        'bank_name' => 'BANK CENTRAL ASIA',
                        'account_number' => '842-171-3359',
                        'account_name' => 'Kadavi Raditya Alvino',
                        'whatsapp_number' => '+62 895-3831-07479',
                    ]),
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'method_key' => 'midtrans',
                    'name' => 'Midtrans Payment Gateway (Snap)',
                    'description' => 'Pembayaran instan online via QRIS, BCA/Mandiri/BRI Virtual Account, Gopay, ShopeePay, & Kartu',
                    'is_active' => false,
                    'config' => null,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'method_key' => 'google_pay',
                    'name' => 'Google Pay',
                    'description' => 'Pembayaran instan melalui dompet digital Google Pay Web',
                    'is_active' => false,
                    'config' => null,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('payment_method_settings')->insert($defaults);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_settings');
    }
};

