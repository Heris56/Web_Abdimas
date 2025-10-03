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
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nisn')->primary(); // primary key string
            $table->string('nama_siswa');
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('tahun_ajaran')->nullable();
            $table->unsignedBigInteger('id_kelas')->nullable();

            // kalau ada relasi
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('set null');
            $table->foreign('tahun_ajaran')->references('id')->on('tahun_ajaran')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
