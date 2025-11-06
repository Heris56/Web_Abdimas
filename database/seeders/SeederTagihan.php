<?php

namespace Database\Seeders;

use App\Models\Tagihan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederTagihan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // manual data tagihan
        Tagihan::create([
            'status_tagihan' => "Belum Lunas",
            'tanggal_pembuatan_tagihan' => now(),
            'nisn' => '20250001',
            'periode' => "sekali",
            'id_tipe_pembayaran' => 2,
            'id_tahun_ajaran' => 1,
            'nominal_tagihan' => 10000000,
        ]);

        // automated create data tagihan
        // Tagihan::factory()->count(30)->create();
    }
}
