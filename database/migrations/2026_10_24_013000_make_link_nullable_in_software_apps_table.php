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
            DB::statement("ALTER TABLE software_apps ALTER COLUMN link DROP NOT NULL;");
        } else {
            Schema::table('software_apps', function (Blueprint $table) {
                $table->text('link')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE software_apps ALTER COLUMN link SET NOT NULL;");
        } else {
            Schema::table('software_apps', function (Blueprint $table) {
                $table->text('link')->nullable(false)->change();
            });
        }
    }
};
