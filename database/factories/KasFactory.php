<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kas;
use App\Models\TipePembayaran;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kas>
 */
class KasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_tipe_pembayaran' => TipePembayaran::inRandomOrder()->value('id_tipe_pembayaran') ?? null, // or an existing tipe_pembayaran ID
            'nama_kas' => $this->faker->word() . ' Cash',
            'saldo' => $this->faker->randomFloat(2, 0, 1000000), // random balance
        ];
    }
}
