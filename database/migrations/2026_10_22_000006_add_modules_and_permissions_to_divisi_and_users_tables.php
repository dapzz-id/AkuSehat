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
        Schema::table('divisi', function (Blueprint $table) {
            $table->json('active_modules')->nullable()->after('color_cover');
            $table->json('module_permissions')->nullable()->after('active_modules');
            $table->string('description', 255)->nullable()->after('module_permissions');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('custom_modules')->nullable()->after('status');
            $table->json('custom_permissions')->nullable()->after('custom_modules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->dropColumn(['active_modules', 'module_permissions', 'description']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['custom_modules', 'custom_permissions']);
        });
    }
};
