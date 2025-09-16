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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kelas')->nullable();
            $table->string('tgl', 20);
            $table->string('nis', 30)->nullable();
            $table->string('username', 20)->unique();
            $table->string('password');
            $table->string('nama', 75);
            $table->enum('level', ['Admin PMR', 'Siswa', 'Guru Olahraga', 'Guru BK']);
            $table->enum('jk', ['L', 'P']);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_kelas')->references('id')->on('kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
