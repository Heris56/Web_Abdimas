<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jurusanList = [
            'IPA 1',
            'IPA 2',
            'IPS 1',
            'IPS 2',
            'Bahasa',
            'TKJ',
            'RPL',
            'Multimedia'
        ];

        return [
            'jurusan' => $this->faker->randomElement($jurusanList),
        ];
    }
}
