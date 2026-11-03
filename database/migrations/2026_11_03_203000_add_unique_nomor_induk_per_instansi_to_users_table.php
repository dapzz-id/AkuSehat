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
        Schema::table('users', function (Blueprint $table) {
            // Composite unique index: nomor_induk unik per instansi_id
            // (nomor_induk boleh sama asalkan beda instansi, username tetap unik global)
            $table->unique(['instansi_id', 'nomor_induk'], 'users_instansi_nomor_induk_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_instansi_nomor_induk_unique');
        });
    }
};
