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
        Schema::create('peminjaman_pita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->index();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->integer('jumlah_pita');
            $table->tinyInteger('verified')->default(0);
            $table->enum('status', ['menunggu', 'dipinjam', 'dikembalikan', 'ditolak', 'terlambat']);
            $table->date('estimasi_selesai_haid');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_pita');
    }
};
