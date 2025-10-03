<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat data dummy 5 tahun ajaran
        TahunAjaran::factory()->count(5)->create();

        // Tandai salah satu sebagai "current"
        TahunAjaran::factory()->create([
            'tahun' => '2024/2025',
            'is_current' => true,
        ]);
    }
}
