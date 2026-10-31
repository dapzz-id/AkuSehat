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
            $table->uuid('id_kesehatan')->primary();
            $table->uuid('id_user')->index();
            $table->uuid('instansi_id')->nullable()->index();
            $table->date('tgl')->index();
            $table->decimal('bb', 5, 2);
            $table->decimal('tb', 5, 2);
            $table->integer('sistol');
            $table->integer('diastol');
            $table->string('status_darah', 30);
            $table->decimal('imt', 4, 2);
            $table->string('status', 30);
            $table->text('pesan_imt');
            $table->text('pesan_tkd'); // Pesan Tekanan Darah
            $table->string('kondisi_telinga')->nullable();
            $table->string('kondisi_gigi')->nullable();
            $table->text('perilaku_beresiko')->nullable();
            $table->text('gangguan_reproduksi')->nullable();
            
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
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
