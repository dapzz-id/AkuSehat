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
        Schema::create('software_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('version')->index();
            $table->string('description')->nullable();
            $table->string('developer')->default('raadeveloperz');
            $table->text('link');
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->text('icon')->nullable();
            $table->enum('platform', ['android', 'ios'])->default('android')->index();
            $table->date('release_date')->default(now())->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_apps');
    }
};
