<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('counseling_sessions', 'ruangan')) {
                $table->string('ruangan', 100)->nullable()->after('session_date');
            }
            if (!Schema::hasColumn('counseling_sessions', 'hasil_konseling')) {
                $table->text('hasil_konseling')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('counseling_sessions', function (Blueprint $table) {
            $table->dropColumn(['ruangan', 'hasil_konseling']);
        });
    }
};
