<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data contoh manual
        Kelas::create(['jurusan' => 'TKJ']);
        Kelas::create(['jurusan' => 'RPL']);

        // Generate dummy data tambahan
        Kelas::factory()->count(5)->create();
    }
}
