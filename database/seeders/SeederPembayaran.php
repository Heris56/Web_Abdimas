<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederPembayaran extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nominal = [125000, 250000, 500000, 1000000];
        for ($i = 0; $i < 10; $i++) {
            Pembayaran::create(attributes: [
                'jumlah_pembayaran' => $nominal[array_rand($nominal)],
                'id_tagihan' => 79,
                "created_at" => Carbon::createFromFormat('d-m-Y', "01-02-2025"),
            ]);
        }
    }
}
