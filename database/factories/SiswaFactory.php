<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nisn' => $this->faker->unique()->numerify('##########'), // 10 digit angka
            'nama_siswa' => $this->faker->name(),
            'password' => Hash::make('password123'), // default password
            'status' => $this->faker->randomElement(['aktif', 'inactive']),
            'tahun_ajaran' => TahunAjaran::inRandomOrder()->value('id') ?? null,
            'id_kelas' => Kelas::inRandomOrder()->value('id_kelas') ?? null,
        ];
    }
}
