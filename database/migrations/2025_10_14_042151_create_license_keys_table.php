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
        Schema::create('license_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instansi_id')->nullable()->index();
            $table->string('key', 20)->unique();
            $table->integer('kuota_pengguna')->default(200)->index();
            $table->enum('status', ['active', 'expired'])->default('active')->index();
            $table->date('tanggal_berakhir')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_keys');
    }
};
