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
            if (!Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'fcm_platform')) {
                $table->string('fcm_platform', 20)->nullable()->after('fcm_token');
            }
            if (!Schema::hasColumn('users', 'fcm_updated_at')) {
                $table->timestamp('fcm_updated_at')->nullable()->after('fcm_platform');
            }
        });

        // Also create user_device_tokens table for multi-device support
        if (!Schema::hasTable('user_device_tokens')) {
            Schema::create('user_device_tokens', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->text('token');
                $table->string('platform', 20)->default('android');
                $table->string('device_model', 100)->nullable();
                $table->timestamp('last_active_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_device_tokens');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'fcm_platform', 'fcm_updated_at']);
        });
    }
};

