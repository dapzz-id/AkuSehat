<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('instansi_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('order_id', 60)->unique();
            $table->string('tipe_transaksi', 30); // perpanjang_layanan, tambah_kuota, kombinasi
            $table->integer('durasi_bulan')->default(0);
            $table->integer('tambah_kuota')->default(0);
            $table->decimal('nominal', 14, 2);
            $table->string('payment_method', 30)->default('google_pay');
            $table->text('payment_token')->nullable();
            $table->string('status', 20)->default('success'); // pending, success, failed
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_transactions');
    }
};
