<?php

namespace Database\Factories;

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
            'nisn' => "0081343076",
            'id_tipe_pembayaran' => $this->faker->numberBetween(1, 7),
        ];
    }
}
