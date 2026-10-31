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
        Schema::table('instansi', function (Blueprint $table) {
            $table->json('active_modules')->nullable()->after('jenis_instansi');
            $table->json('module_permissions')->nullable()->after('active_modules');
            $table->json('custom_role_labels')->nullable()->after('module_permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instansi', function (Blueprint $table) {
            $table->dropColumn(['active_modules', 'module_permissions', 'custom_role_labels']);
        });
    }
};
