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
        Schema::table('peminjaman_pita', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman_pita', 'denda_nominal')) {
                $table->integer('denda_nominal')->default(0);
            }
            if (!Schema::hasColumn('peminjaman_pita', 'denda_status')) {
                $table->string('denda_status', 30)->default('nihil'); // nihil, belum_lunas, lunas
            }
            if (!Schema::hasColumn('peminjaman_pita', 'sanksi_catatan')) {
                $table->string('sanksi_catatan', 255)->nullable();
            }
            if (!Schema::hasColumn('peminjaman_pita', 'health_flag')) {
                $table->string('health_flag', 40)->default('normal'); // normal, perhatian_khusus, rujuk_konseling
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_pita', function (Blueprint $table) {
            $table->dropColumn(['denda_nominal', 'denda_status', 'sanksi_catatan', 'health_flag']);
        });
    }
};
