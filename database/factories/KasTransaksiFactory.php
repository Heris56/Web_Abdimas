<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KasTransaksi;
use App\Models\Kas;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KasTransaksi>
 */
class KasTransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = KasTransaksi::class;

    public function definition(): array
    {
        $kas = Kas::inRandomOrder()->first();

        // Randomly decide if it's a debit or credit
        $debit = $this->faker->boolean(50) ? $this->faker->randomFloat(2, 100, 10000) : 0;
        $kredit = $debit === 0 ? $this->faker->randomFloat(2, 100, 10000) : 0;

        // Calculate saldo_akhir as kas.saldo ± transaction
        $saldo_akhir = $kas ? $kas->saldo + $debit - $kredit : $debit - $kredit;

        return [
            'id_kas' => $kas ? $kas->id_kas : Kas::factory(), // fallback to new Kas
            'sumber' => $this->faker->randomElement(['penjualan', 'pembayaran', 'pengeluaran']),
            'id_sumber' => $this->faker->numberBetween(1, 100),
            'tanggal' => $this->faker->date(),
            'keterangan' => $this->faker->sentence(),
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo_akhir' => $saldo_akhir,
        ];
    }
}
