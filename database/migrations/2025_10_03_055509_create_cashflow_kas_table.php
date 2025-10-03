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
        Schema::create('cashflow_kas', function (Blueprint $table) {
            $table->id('id_kas');
            $table->unsignedBigInteger('id_tipe_pembayaran');
            $table->string('nama_kas');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_tipe_pembayaran')
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
        Schema::dropIfExists('cashflow_kas');
    }
};
