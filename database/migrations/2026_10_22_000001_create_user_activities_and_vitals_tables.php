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
        // 1. User Activities (GPS Run Tracker, Walk, Cycling)
        Schema::create('user_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('instansi_id')->nullable()->index();
            $table->string('activity_type', 30)->default('run')->index(); // run, walk, cycling
            $table->string('title')->nullable();
            $table->decimal('distance_meters', 10, 2)->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->decimal('calories', 8, 2)->default(0);
            $table->string('avg_pace', 10)->nullable(); // e.g. "05:30"
            $table->decimal('max_speed', 6, 2)->nullable(); // km/h
            $table->unsignedInteger('steps')->default(0);
            $table->json('route_coordinates')->nullable(); // [{"latitude": ..., "longitude": ..., "timestamp": ...}]
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
        });

        // 2. User Daily Vital Logs (Hydration, Sleep, Mood, BP)
        Schema::create('user_vital_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('instansi_id')->nullable()->index();
            $table->date('log_date')->index();
            $table->unsignedInteger('water_intake_ml')->default(0);
            $table->decimal('sleep_hours', 4, 1)->nullable();
            $table->tinyInteger('mood_score')->nullable(); // 1-5
            $table->unsignedSmallInteger('systolic')->nullable();
            $table->unsignedSmallInteger('diastolic')->nullable();
            $table->decimal('blood_sugar', 6, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'log_date']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vital_logs');
        Schema::dropIfExists('user_activities');
    }
};
