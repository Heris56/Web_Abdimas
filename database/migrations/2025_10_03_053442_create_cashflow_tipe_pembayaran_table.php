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
        Schema::create('cashflow_tipe_pembayaran', function (Blueprint $table) {
            $table->id('id_tipe_pembayaran');
            $table->string('nama_tipe');
            $table->string('tipe_periodik')->nullable(); // contoh: bulanan, tahunan, semester, sekali
            $table->boolean('is_bulanan')->default(false);
            $table->boolean('is_sekali_bayar')->default(false);
            $table->boolean('is_pertaun')->default(false);
            $table->boolean('is_persemester')->default(false);
            $table->text('keterangan')->nullable();
            $table->decimal('nominal', 12, 2)->default(0); // nominal pembayaran
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_tipe_pembayaran');
    }
};
