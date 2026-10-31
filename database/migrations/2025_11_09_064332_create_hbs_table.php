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
            $table->uuid('id_hb')->primary();
            $table->uuid('id_user')->index();
            $table->uuid('id_divisi')->nullable()->index();
            $table->uuid('instansi_id')->nullable()->index();
            $table->date('tgl')->index();
            $table->decimal('hb', 4, 2);
            $table->string('status', 30);
            $table->text('pesan');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_divisi')->references('id')->on('divisi')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('instansi_id')->references('id')->on('instansi')->onDelete('cascade')->onUpdate('cascade');
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
