<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop redundant duplicate index on divisi
        try {
            DB::statement("DROP INDEX IF EXISTS idx_divisi_instansi");
        } catch (\Throwable $e) {}

        // 2. Add composite index on billing_transactions (instansi_id, created_at)
        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->index(['instansi_id', 'created_at'], 'idx_billing_instansi_created');
        });

        // 3. Add composite index on data_haid (instansi_id, tanggal_mulai)
        Schema::table('data_haid', function (Blueprint $table) {
            $table->index(['instansi_id', 'tanggal_mulai'], 'idx_data_haid_instansi_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_haid', function (Blueprint $table) {
            $table->dropIndex('idx_data_haid_instansi_mulai');
        });

        Schema::table('billing_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_billing_instansi_created');
        });

        try {
            DB::statement("CREATE INDEX idx_divisi_instansi ON divisi (instansi_id)");
        } catch (\Throwable $e) {}
    }
};
