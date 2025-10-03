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
        Schema::create('cashflow_kas_transaksi', function (Blueprint $table) {
            $table->id('id_kas_transaksi');
            $table->unsignedBigInteger('id_kas');
            $table->string('sumber')->nullable();
            $table->unsignedBigInteger('id_sumber')->nullable();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2)->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_kas')
                ->references('id_kas')
                ->on('cashflow_kas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_kas_transaksi');
    }
};
