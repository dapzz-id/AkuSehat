<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique()->after('username');
            }
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->index()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
        });

        Schema::table('instansi', function (Blueprint $table) {
            if (!Schema::hasColumn('instansi', 'invite_code')) {
                $table->string('invite_code', 30)->nullable()->unique()->index()->after('npsn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'google_id', 'avatar']);
        });

        Schema::table('instansi', function (Blueprint $table) {
            $table->dropColumn(['invite_code']);
        });
    }
};
