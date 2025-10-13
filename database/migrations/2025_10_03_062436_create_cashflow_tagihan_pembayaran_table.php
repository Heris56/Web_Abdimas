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
        Schema::create('cashflow_tagihan_pembayaran', function (Blueprint $table) {
            $table->id('id_tagihan_pembayaran'); // primary key
            $table->unsignedBigInteger('id_pembayaran'); // foreign key to Tagihan
            $table->decimal('jumlah_pembayaran', 15, 2)->default(0); // payment amount
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('id_pembayaran')
                ->references('id_pembayaran')
                ->on('tagihan') // adjust if your table name is different
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_tagihan_pembayaran');
    }
};
