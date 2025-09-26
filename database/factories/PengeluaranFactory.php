<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengeluaran>
 */
class PengeluaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nominal' => $this->faker->numberBetween(10000, 1000000),
            'keterangan' => $this->faker->sentence(3),
            'tanggal' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'id_kas' => $this->faker->numberBetween(1, 7),
        ];
    }
}
