<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('tahun');
            $table->tinyInteger('active_year_marker')
                ->virtualAs('CASE WHEN is_current = 1 THEN 1 ELSE NULL END')
                ->nullable()
                ->unique()
                ->after('is_current');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            //
        });
    }
};
