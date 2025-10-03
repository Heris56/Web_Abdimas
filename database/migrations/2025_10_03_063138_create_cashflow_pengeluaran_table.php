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
        Schema::create('cashflow_pengeluaran', function (Blueprint $table) {
            $table->id('id_pengeluaran'); // primary key
            $table->decimal('nominal', 15, 2); // amount
            $table->text('keterangan')->nullable(); // description
            $table->date('tanggal'); // date of expense
            $table->unsignedBigInteger('id_kas'); // foreign key to TipePembayaran
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_kas')
                ->references('id_tipe_pembayaran')
                ->on('cashflow_tipe_pembayaran')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_pengeluaran');
    }
};
