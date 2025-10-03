<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            StaffKeuanganSeeder::class,
            KelasSeeder::class, // siswa butuh kelas duluan
            TahunAjaranSeeder::class, // siswa butuh tahun ajaran duluan
            SiswaSeeder::class,
            ActivityLogsSeeder::class,
            TipePembayaranSeeder::class,
            SeederTagihan::class,
            KasSeeder::class,
            KasTransaksiSeeder::class,
        ]);
    }
}
