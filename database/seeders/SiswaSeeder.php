<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // contoh manual
        Siswa::create([
            'nisn' => '20250001',
            'nama_siswa' => 'Relana Mordred',
            'password' => Hash::make('rahasia123'),
            'status' => 'aktif',
            'tahun_ajaran' => 1, // pastikan ada id di tahun_ajaran
            'id_kelas' => "XII-RPL-1", // pastikan ada id di kelas
        ]);

        // generate dummy 10 siswa pakai factory
        // Siswa::factory()->count(10)->create();
    }
}
