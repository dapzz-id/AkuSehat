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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE software_apps DROP CONSTRAINT IF EXISTS software_apps_platform_check");
            DB::statement("ALTER TABLE software_apps ADD CONSTRAINT software_apps_platform_check CHECK (platform IN ('android', 'ios', 'web'))");
        }

        Schema::table('software_apps', function (Blueprint $table) {
            if (!Schema::hasColumn('software_apps', 'is_mandatory')) {
                $table->boolean('is_mandatory')->default(false);
            }
            if (!Schema::hasColumn('software_apps', 'min_version')) {
                $table->string('min_version')->nullable();
            }
            if (!Schema::hasColumn('software_apps', 'changelog')) {
                $table->text('changelog')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_apps', function (Blueprint $table) {
            if (Schema::hasColumn('software_apps', 'is_mandatory')) {
                $table->dropColumn('is_mandatory');
            }
            if (Schema::hasColumn('software_apps', 'min_version')) {
                $table->dropColumn('min_version');
            }
            if (Schema::hasColumn('software_apps', 'changelog')) {
                $table->dropColumn('changelog');
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE software_apps DROP CONSTRAINT IF EXISTS software_apps_platform_check");
            DB::statement("ALTER TABLE software_apps ADD CONSTRAINT software_apps_platform_check CHECK (platform IN ('android', 'ios'))");
        }
    }
};
