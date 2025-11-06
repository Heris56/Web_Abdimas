<?php

namespace Database\Seeders;

use App\Models\StaffKeuangan;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StaffKeuangan::insert([
            [
                'nama' => 'Darryl Rambi',
                'email' => 'darryl@example.com',
                'password' => Hash::make('rahasia123'),
                'status' => 'active',
            ],
            [
                'nama' => 'Dafa Raimi',
                'email' => 'dafa@example.com',
                'password' => Hash::make('rahasia123'),
                'status' => 'active',
            ],
            [
                'nama' => 'Raphael Permana',
                'email' => 'raphael@example.com',
                'password' => Hash::make('rahasia123'),
                'status' => 'active',
            ],
            [
                'nama' => 'Haikal Risnandar',
                'email' => 'haikal@example.com',
                'password' => Hash::make('rahasia123'),
                'status' => 'active',
            ],
        ]);
    }
}
