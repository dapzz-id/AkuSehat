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
        // 1. Audit / Activity Logs
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('user_role')->nullable();
                $table->uuid('instansi_id')->nullable()->index();
                $table->string('instansi_name')->nullable();
                $table->string('action')->index();
                $table->string('category')->default('system')->index();
                $table->text('description');
                $table->json('properties')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device', 50)->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }

        // 2. Global Module Overrides / Feature Flags (SuperAdmin Master Control)
        if (!Schema::hasTable('global_module_configs')) {
            Schema::create('global_module_configs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('module_key')->unique()->index();
                $table->string('name')->nullable();
                $table->string('status', 30)->default('active')->index(); // 'active', 'maintenance', 'invisible', 'tester_only'
                $table->text('maintenance_message')->nullable();
                $table->json('tester_user_ids')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->timestamps();
            });
        }

        // 3. User tester flag
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_tester')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_tester')->default(false)->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_tester')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_tester');
            });
        }
        Schema::dropIfExists('global_module_configs');
        Schema::dropIfExists('audit_logs');
    }
};
