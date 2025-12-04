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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_divisi')->nullable()->index();
            $table->unsignedBigInteger('instansi_id')->nullable()->index();
            $table->unsignedBigInteger('license_key_id')->nullable()->index();
            $table->string('tgl', 20);
            $table->string('nomor_induk', 30)->nullable();
            $table->string('username', 20)->unique();
            $table->string('password')->index();
            $table->string('nama', 75);
            $table->enum('level', ['Admin', 'Member', 'Health Consultant', 'Health Monitor', 'SuperAdmin', 'Admin Instansi'])->default('Member')->index();
            $table->enum('jk', ['L', 'P'])->index();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_divisi')->references('id')->on('divisi')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('license_key_id')->references('id')->on('license_keys')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
