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
        Schema::table('user_activities', function (Blueprint $table) {
            $indexes = collect(DB::select('SHOW INDEX FROM user_activities'))->pluck('Key_name')->unique();
            if (!$indexes->contains('idx_activities_started_at')) {
                $table->index('started_at', 'idx_activities_started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropIndex('idx_activities_started_at');
        });
    }
};
