<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipePembayaran>
 */
class TipePembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_tipe' => $this->faker->randomElement(['SPP', 'PSB', 'PTS', 'PAS', 'PKL', 'Kunjungan Industri', 'Ujian Sekolah']),
            'tipe_periodik' => $this->faker->randomElement(['bulanan', 'tahunan', 'semester', 'sekali']),
            'is_bulanan' => $this->faker->boolean(30),
            'is_sekali_bayar' => $this->faker->boolean(20),
            'is_pertaun' => $this->faker->boolean(20),
            'is_persemester' => $this->faker->boolean(30),
            'keterangan' => $this->faker->sentence(),
            'nominal' => $this->faker->numberBetween(100000, 2000000),
        ];
    }
}
