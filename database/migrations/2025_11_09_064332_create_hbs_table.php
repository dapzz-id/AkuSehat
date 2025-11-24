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
        Schema::create('hb', function (Blueprint $table) {
            $table->id('id_hb');
            $table->unsignedBigInteger('id_user')->index();
            $table->unsignedBigInteger('id_kelas')->index();
            $table->date('tgl')->index();
            $table->string('hb', 30); // Kadar Hemoglobin
            $table->string('status', 30);
            $table->text('pesan');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hb');
    }
};
