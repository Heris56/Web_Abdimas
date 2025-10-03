<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StaffKeuangan;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;

class PembayaranTest extends TestCase
{
    private function getTestStaff(): StaffKeuangan
    {
        $staff = StaffKeuangan::where('email', 'test_only@example.com')->first();
        if (!$staff) {
            $staff = StaffKeuangan::create([
                'email' => 'test_only@example.com',
                'password' => bcrypt('newtest1234'),
                'nama' => 'Test Staff',
                'status' => 'Aktif'
            ]);
        }
        return $staff;
    }

    /** @test */
    public function authenticated_staff_can_fetch_pembayaran_list()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/getpembayaran');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_fetch_data_pembayaran_with_relationships()
    {
        $response = $this->getJson('/api/datapembayaran');
        $response->assertStatus(200)->assertJsonStructure(['message', 'data', 'meta']);
    }

    /** @test */
    public function unauthenticated_user_cannot_fetch_pembayaran()
    {
        $response = $this->getJson('/api/getpembayaran');
        $response->assertStatus(401);
    }


}
