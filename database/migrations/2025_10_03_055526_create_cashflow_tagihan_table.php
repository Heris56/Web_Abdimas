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
        Schema::create('cashflow_tagihan', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->string('status_pembayaran')->default('belum_bayar'); // contoh default
            $table->date('tanggal_pembuatan_tagihan');
            $table->string('nisn'); // relasi ke siswa
            $table->string('periode')->nullable();
            $table->unsignedBigInteger('id_tipe_pembayaran');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->decimal('nominal_tagihan', 12, 2);

            $table->softDeletes();
            $table->timestamps();

            // foreign keys
            $table->foreign('nisn')->references('nisn')->on('siswa')->onDelete('cascade');
            $table->foreign('id_tipe_pembayaran')->references('id_tipe_pembayaran')->on('cashflow_tipe_pembayaran')->onDelete('cascade');
            $table->foreign('id_tahun_ajaran')->references('id')->on('tahun_ajaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_tagihan');
    }
};
