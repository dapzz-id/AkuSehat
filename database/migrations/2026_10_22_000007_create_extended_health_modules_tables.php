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
        // 1. Counseling & SafeSpace
        if (!Schema::hasTable('counseling_sessions')) {
            Schema::create('counseling_sessions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('instansi_id')->index();
                $table->uuid('user_id')->index();
                $table->uuid('consultant_id')->nullable()->index();
                $table->dateTime('session_date');
                $table->string('topic', 150);
                $table->text('notes')->nullable();
                $table->enum('status', ['menunggu', 'terjadwal', 'selesai', 'dibatalkan'])->default('menunggu');
                $table->boolean('is_confidential')->default(true);
                $table->timestamps();

                $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('consultant_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 2. Nutrition Logs
        if (!Schema::hasTable('nutrition_logs')) {
            Schema::create('nutrition_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->uuid('instansi_id')->nullable()->index();
                $table->date('log_date')->index();
                $table->string('meal_type', 30)->default('makan_siang');
                $table->string('food_name', 200);
                $table->integer('calories')->default(0);
                $table->boolean('is_balanced')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
            });
        }

        // 3. Burnout & Mental Stress Screening
        if (!Schema::hasTable('burnout_screenings')) {
            Schema::create('burnout_screenings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->uuid('instansi_id')->nullable()->index();
                $table->date('screening_date')->index();
                $table->integer('stress_score')->default(25); // 0 - 100
                $table->enum('burnout_level', ['rendah', 'sedang', 'tinggi'])->default('rendah');
                $table->integer('workload_rating')->default(3); // 1 - 5
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
            });
        }

        // 4. Ergonomics & Sedentary Logs
        if (!Schema::hasTable('ergonomics_logs')) {
            Schema::create('ergonomics_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->uuid('instansi_id')->nullable()->index();
                $table->date('log_date')->index();
                $table->integer('sedentary_minutes')->default(240); // menit duduk diam
                $table->integer('breaks_taken')->default(3); // kali peregangan
                $table->string('posture_complaint', 100)->nullable(); // leher, punggung, mata, pergelangan
                $table->integer('score')->default(80); // 0 - 100
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
            });
        }

        // 5. Vaccine & Immunization Records
        if (!Schema::hasTable('vaccine_records')) {
            Schema::create('vaccine_records', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->index();
                $table->uuid('instansi_id')->nullable()->index();
                $table->string('vaccine_name', 150);
                $table->integer('dose_number')->default(1);
                $table->date('administered_date');
                $table->string('batch_number', 50)->nullable();
                $table->string('administered_by', 100)->nullable();
                $table->text('allergy_notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_records');
        Schema::dropIfExists('ergonomics_logs');
        Schema::dropIfExists('burnout_screenings');
        Schema::dropIfExists('nutrition_logs');
        Schema::dropIfExists('counseling_sessions');
    }
};
