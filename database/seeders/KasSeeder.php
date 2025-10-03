<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kas;


class KasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kas::factory()->count(10)->create();
    }
}
