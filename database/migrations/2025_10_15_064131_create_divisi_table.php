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
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instansi_id')->index();
            $table->string('tgl', 20)->index();
            $table->string('divisi_name', 100)->index();
            $table->string('color_cover')->default('#2e7d32');
            $table->timestamps();

            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
