<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StaffKeuangan;
use App\Models\TipePembayaran;

class TipePembayaranTest extends TestCase
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
    public function authenticated_staff_can_fetch_tipe_pembayaran_list()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/gettipepembayaran');
        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_tipe_pembayaran_and_auto_cleanup()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;
        $uniqueName = 'TEST_CREATE_' . time();

        $response = $this->withToken($token)->postJson('/api/inserttipepembayaran', [
            'nama_tipe' => $uniqueName,
            'nominal' => 500000,
            'tipe_periodik' => 'bulanan',
            'keterangan' => 'Test pembayaran - will be cleaned up'
        ]);

        $response->assertStatus(201);
        TipePembayaran::where('nama_tipe', $uniqueName)->delete();
    }

    /** @test */
    public function update_tipe_pembayaran_fails_for_nonexistent_record()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/updatetipepembayaran/99999999', [
            'nama_tipe' => 'Test',
            'nominal' => 500000,
            'tipe_periodik' => 'bulanan'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function can_delete_tipe_pembayaran_with_safe_test_data()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $tipe = TipePembayaran::create([
            'nama_tipe' => 'TEST_DELETE_' . time(),
            'nominal' => 100000,
            'tipe_periodik' => 'sekali',
            'keterangan' => 'Created only to be deleted'
        ]);

        $response = $this->withToken($token)->deleteJson("/api/deletetipepembayaran/{$tipe->id_tipe_pembayaran}");
        $response->assertStatus(200);
    }
}
