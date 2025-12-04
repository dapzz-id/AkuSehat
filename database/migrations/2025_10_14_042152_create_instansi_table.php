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
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_id_active')->nullable()->index();
            $table->string('nama_instansi')->index();
            $table->string('alamat');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('website')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->timestamps();

            $table->foreign('license_id_active')->references('id')->on('license_keys')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instansi');
    }
};
