<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use App\Models\TipePembayaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tagihan>
 */
class TagihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status_pembayaran' => $this->faker->randomElement(['lunas', 'belum']),
            'tanggal_pembuatan_tagihan' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'nisn' => Siswa::inRandomOrder()->value('nisn'), // ambil random dari data existing
            'periode' => $this->faker->randomElement(['2025-1', '2025-2']),
            'id_tipe_pembayaran' => TipePembayaran::inRandomOrder()->value('id_tipe_pembayaran'),
            'id_tahun_ajaran' => TahunAjaran::inRandomOrder()->value('id'),
            'nominal_tagihan' => $this->faker->numberBetween(50000, 2000000),
        ];
    }
}
