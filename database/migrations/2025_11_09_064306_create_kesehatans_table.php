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
        Schema::create('kesehatan', function (Blueprint $table) {
            $table->id('id_kesehatan');
            $table->unsignedBigInteger('id_user')->index();
            $table->date('tgl')->index();
            $table->string('bb', 30); // Berat Badan
            $table->string('tb', 30); // Tinggi Badan
            $table->string('sistol', 30);
            $table->string('diastol', 30);
            $table->string('status_darah', 30);
            $table->string('imt', 20); // Indeks Massa Tubuh
            $table->string('status', 30);
            $table->text('pesan_imt');
            $table->text('pesan_tkd'); // Pesan Tekanan Darah
            $table->string('kondisi_telinga')->nullable();
            $table->string('kondisi_gigi')->nullable();
            $table->text('perilaku_beresiko')->nullable();
            $table->text('gangguan_reproduksi')->nullable();
            
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesehatan');
    }
};
