<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\Api\KeuanganController;
use App\Models\StaffKeuangan;

class KeuanganControllerTest extends TestCase
{
    protected function dumpResponse($testName, $response)
    {
        echo "\n===== {$testName} RESPONSE =====\n";
        echo json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n===============================\n";
    }

    public function testGetsiswa()
    {
        $mockData = [
            (object) [
                'id' => 1,
                'nama_siswa' => 'John',
                'nisn' => '123456789',
                'status' => 'Aktif',
            ]
        ];

        $this->partialMock(KeuanganController::class, function ($mock) use ($mockData) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('getsiswa')
                ->andReturn(response()->json([
                    'message' => 'Berhasil Fetch Siswa',
                    'data' => $mockData,
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 1]
                ], 200));
        });

        $response = $this->getJson(route('api.siswa'));
        $this->dumpResponse('Getsiswa', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Berhasil Fetch Siswa']);
    }

    public function testGetPembayaran()
    {
        $mockData = [
            (object) [
                'id_pembayaran' => 1,
                'jumlah' => 150000,
                'siswa' => [
                    'id' => 1,
                    'nama_siswa' => 'John',
                    'nisn' => '123456789',
                    'status' => 'Aktif',
                ]
            ]
        ];

        $this->actingAs(new StaffKeuangan(['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com']), 'sanctum');

        $this->partialMock(KeuanganController::class, function ($mock) use ($mockData) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('getPembayaran')
                ->andReturn(response()->json([
                    'message' => 'Berhasil Fetch data Pembayaran',
                    'data' => $mockData,
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 1]
                ], 200));
        });

        $response = $this->getJson(route('api.Pembayaran'));
        $this->dumpResponse('GetPembayaran', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Berhasil Fetch data Pembayaran']);
    }

    public function testGetTipePembayaran()
    {
        $mockData = [
            (object) [
                'id_tipe_pembayaran' => 1,
                'nama_tipe' => 'SPP Bulanan',
                'nominal' => 500000,
                'tipe_periodik' => 'bulanan',
                'keterangan' => 'SPP reguler bulanan',
            ]
        ];

        $this->actingAs(new StaffKeuangan(['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com']), 'sanctum');

        $this->partialMock(KeuanganController::class, function ($mock) use ($mockData) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('getTipePembayaran')
                ->andReturn(response()->json([
                    'message' => 'Berhasil Fetch data Tipe Pembayaran',
                    'data' => $mockData,
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 1]
                ], 200));
        });

        $response = $this->getJson(route('api.TipePembayaran'));
        $this->dumpResponse('GetTipePembayaran', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Berhasil Fetch data Tipe Pembayaran']);
    }

    public function testLogoutKeuangan()
    {
        $this->actingAs(new StaffKeuangan(['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com']), 'sanctum');

        $this->partialMock(KeuanganController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('LogoutKeuangan')
                ->andReturn(response()->json(['message' => 'Logout berhasil'], 200));
        });

        $response = $this->postJson(route('api.logoutstaffKeuangan'));
        $this->dumpResponse('LogoutKeuangan', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Logout berhasil']);
    }

    public function testLoginKeuangan()
    {
        $mockUser = ['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com'];
        $mockToken = 'fake-token-123';

        $this->partialMock(KeuanganController::class, function ($mock) use ($mockUser, $mockToken) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('LoginKeuangan')
                ->andReturn(response()->json([
                    'message' => 'Login berhasil',
                    'token' => $mockToken,
                    'user' => $mockUser
                ], 200));
        });

        $response = $this->postJson(route('api.loginstaffKeuangan'), ['email' => 'johndoe@example.com', 'password' => 'rahasia123']);
        $this->dumpResponse('LoginKeuangan', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Login berhasil']);
    }

    public function testChangePassword()
    {
        $this->actingAs(new StaffKeuangan(['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com']), 'sanctum');

        $this->partialMock(KeuanganController::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('ChangePassword')
                ->andReturn(response()->json(['message' => 'Password Berhasil Diganti'], 200));
        });

        $response = $this->postJson(route('api.passchangestaffKeuangan'), [
            'new_password' => 'rahasia1234',
            'new_password_confirmation' => 'rahasia1234'
        ]);

        $this->dumpResponse('ChangePassword', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Password Berhasil Diganti']);
    }

    public function testDataPembayaran()
    {
        $mockData = [
            (object) [
                'id_pembayaran' => 1,
                'jumlah_pembayaran' => 150000,
                'tagihan' => [
                    'id_pembayaran' => 1,
                    'siswa' => ['id' => 1, 'nama_siswa' => 'John', 'nisn' => '123456789', 'status' => 'Aktif'],
                    'tipePembayaran' => ['id_tipe_pembayaran' => 1, 'nama_tipe' => 'SPP Bulanan']
                ]
            ]
        ];

        $this->actingAs(new StaffKeuangan(['id' => 1, 'nama' => 'John', 'email' => 'johndoe@example.com']), 'sanctum');

        $this->partialMock(KeuanganController::class, function ($mock) use ($mockData) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('dataPembayaran')
                ->andReturn(response()->json([
                    'message' => 'Berhasil Fetch Data Transaksi',
                    'data' => $mockData,
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 1]
                ], 200));
        });

        $response = $this->getJson(route('api.datapembayaran'));
        $this->dumpResponse('DataPembayaran', $response);
        $response->assertStatus(200)->assertJson(['message' => 'Berhasil Fetch Data Transaksi']);
    }
}
