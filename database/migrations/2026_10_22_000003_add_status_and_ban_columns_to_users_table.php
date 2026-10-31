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
            $table->string('status', 20)->default('aktif')->after('level')->index(); // 'aktif', 'suspended', 'banned'
            $table->dateTime('suspended_until')->nullable()->after('status')->index();
            $table->text('ban_reason')->nullable()->after('suspended_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'suspended_until', 'ban_reason']);
        });
    }
};
