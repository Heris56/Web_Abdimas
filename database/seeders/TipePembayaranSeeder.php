<?php

namespace Database\Seeders;

use App\Models\TipePembayaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate dummy random data
        TipePembayaran::factory()->count(10)->create();

        // Tambahkan beberapa data fix
        TipePembayaran::create([
            'nama_tipe' => 'SPP',
            'tipe_periodik' => 'bulanan',
            'is_bulanan' => true,
            'nominal' => 500000,
            'keterangan' => 'Pembayaran SPP per bulan',
        ]);

        TipePembayaran::create([
            'nama_tipe' => 'Ujian Sekolah',
            'tipe_periodik' => 'sekali',
            'is_sekali_bayar' => true,
            'nominal' => 2500000,
            'keterangan' => 'Dibayarkan sekali saat masuk',
        ]);
    }
}
