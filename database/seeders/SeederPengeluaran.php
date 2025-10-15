<?php

namespace Database\Seeders;

use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederPengeluaran extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nominal = [1250000, 2500000, 5000000, 10000000];

        Pengeluaran::create([
            'nominal' => $nominal[array_rand($nominal)],
            'keterangan' => "Test populate",
            'tanggal' => Carbon::createFromFormat('d-m-Y', "01-07-2025"),
            'id_kas' => 3,
        ]);

        // auto create pengeluaran
        // Pengeluaran::factory()->count(30)->create();
    }
}
