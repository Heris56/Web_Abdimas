<?php

use App\Models\StaffKeuangan;
use App\Models\Siswa;
use App\Models\TipePembayaran;
use App\Models\Tagihan;
use App\Models\Pembayaran;

// Helper function to get or create test staff (separate from production data)
function getTestStaff(): StaffKeuangan
{
    $staff = StaffKeuangan::where('email', 'test_only@example.com')->first();

    if (!$staff) {
        $staff = StaffKeuangan::create([
            'email' => 'test_only@example.com',
            'password' => bcrypt('newtest1234'),
            'nama' => 'Test Staff (Auto-generated)',
            'status' => 'Aktif'
        ]);
    }

    return $staff;
}

// Cleanup function to remove test data
function cleanupTestData()
{
    // Remove test staff
    StaffKeuangan::where('email', 'test_only@example.com')->delete();

    // Remove test tipe pembayaran
    TipePembayaran::where('nama_tipe', 'LIKE', 'TEST_%')->delete();
}

// ==================== Login Tests (SAFE - Read Only) ====================

test('staff can login with valid credentials', function () {
    $staff = getTestStaff();

    $response = $this->postJson('/api/loginstaff', [
        'email' => 'test_only@example.com',
        'password' => 'newtest1234'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'token',
            'user' => ['id', 'nama', 'email']
        ])
        ->assertJson([
            'message' => 'Login berhasil',
            'user' => [
                'email' => 'test_only@example.com'
            ]
        ]);
});

test('staff cannot login with invalid email', function () {
    $response = $this->postJson('/api/loginstaff', [
        'email' => 'nonexistent@example.com',
        'password' => 'newtest1234'
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'email atau password salah']);
});

test('staff cannot login with invalid password', function () {
    $staff = getTestStaff();

    $response = $this->postJson('/api/loginstaff', [
        'email' => 'test_only@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'email atau password salah']);
});

// ==================== Logout Tests (SAFE - Uses temporary token) ====================

test('authenticated staff can logout', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/logoutstaff');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logout berhasil']);

    // Token is automatically deleted, safe for testing
});

test('unauthenticated user cannot logout', function () {
    $response = $this->postJson('/api/logoutstaff');

    $response->assertStatus(401);
});

// ==================== Change Password Tests (SAFE - Uses test account) ====================

test('authenticated staff can change password', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/passchangestaff', [
            'new_password' => 'newtest12345',
            'new_password_confirmation' => 'newtest12345'
        ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password Berhasil Diganti']);

    // Restore original password for other tests
    $staff->password = bcrypt('newtest1234');
    $staff->save();
});

test('change password fails with mismatched confirmation', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/passchangestaff', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword'
        ]);

    $response->assertStatus(422);
});

test('change password fails with short password', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/passchangestaff', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short'
        ]);

    $response->assertStatus(422);
});

// ==================== GetSiswa Tests (SAFE - Read Only) ====================

test('can fetch siswa list', function () {
    $response = $this->getJson('/api/getsiswa');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['nisn', 'nama_siswa', 'status']
            ],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ])
        ->assertJson(['message' => 'Berhasil Fetch Siswa']);
});

test('can search siswa by name', function () {
    $response = $this->getJson('/api/getsiswa?search=a');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data',
            'meta'
        ]);
});

test('can search siswa by nisn', function () {
    $siswa = Siswa::first();

    if ($siswa) {
        $response = $this->getJson("/api/getsiswa?search={$siswa->nisn}");

        $response->assertStatus(200)
            ->assertJsonFragment(['nisn' => $siswa->nisn]);
    } else {
        $this->markTestSkipped('No siswa data available');
    }
});

test('can filter siswa by status', function () {
    $response = $this->getJson('/api/getsiswa?status=Aktif');

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'data', 'meta']);
});

test('can paginate siswa results', function () {
    $response = $this->getJson('/api/getsiswa?per_page=5');

    $response->assertStatus(200)
        ->assertJsonPath('meta.per_page', 5)
        ->assertJsonStructure(['message', 'data', 'meta']);
});

// ==================== GetPembayaran Tests (SAFE - Read Only) ====================

test('authenticated staff can fetch pembayaran list', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson('/api/getpembayaran');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ])
        ->assertJson(['message' => 'Berhasil Fetch data Pembayaran']);
});

test('unauthenticated user cannot fetch pembayaran', function () {
    $response = $this->getJson('/api/getpembayaran');

    $response->assertStatus(401);
});

// ==================== GetTipePembayaran Tests (SAFE - Read Only) ====================

test('authenticated staff can fetch tipe pembayaran list', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson('/api/gettipepembayaran');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['id_tipe_pembayaran', 'nama_tipe', 'nominal', 'tipe_periodik']
            ],
            'meta'
        ])
        ->assertJson(['message' => 'Berhasil Fetch data Tipe Pembayaran']);
});

test('can search tipe pembayaran by name', function () {
    $staff = getTestStaff();
    $token = $staff->createToken('test-token')->plainTextToken;

    $tipe = TipePembayaran::first();

    if ($tipe) {
        $searchTerm = substr($tipe->nama_tipe, 0, 3);
        $response = $this->withToken($token)
            ->getJson("/api/gettipepembayaran?search={$searchTerm}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data', 'meta']);
    } else {
        $this->markTestSkipped('No tipe pembayaran data available');
    }
});

// ==================== DataPembayaran Tests (SAFE - Read Only) ====================

test('can fetch data pembayaran with relationships', function () {
    $response = $this->getJson('/api/datapembayaran');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data',
            'meta'
        ]);
});

// ==================== Tipe Pembayaran CRUD Tests (SAFE - Creates & Cleans Up) ====================

test('can create tipe pembayaran and auto cleanup', function () {
    $uniqueName = 'TEST_CREATE_' . time();

    $response = $this->postJson('/api/inserttipepembayaran', [
        'nama_tipe' => $uniqueName,
        'nominal' => 500000,
        'tipe_periodik' => 'bulanan',
        'keterangan' => 'Test pembayaran - will be cleaned up'
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id_tipe_pembayaran', 'nama_tipe', 'nominal', 'tipe_periodik']
        ])
        ->assertJson([
            'message' => 'Tipe Pembayaran created successfully',
            'data' => [
                'nama_tipe' => $uniqueName,
                'nominal' => 500000,
                'tipe_periodik' => 'bulanan'
            ]
        ]);

    // Cleanup: Delete the test record
    $createdId = $response->json('data.id_tipe_pembayaran');
    TipePembayaran::where('id_tipe_pembayaran', $createdId)->delete();
});

test('create tipe pembayaran fails with invalid tipe_periodik', function () {
    $response = $this->postJson('/api/inserttipepembayaran', [
        'nama_tipe' => 'TEST_INVALID',
        'nominal' => 500000,
        'tipe_periodik' => 'invalid_type',
        'keterangan' => 'Test'
    ]);

    $response->assertStatus(422);
});

test('create tipe pembayaran fails with missing required fields', function () {
    $response = $this->postJson('/api/inserttipepembayaran', [
        'nama_tipe' => 'TEST_INCOMPLETE'
    ]);

    $response->assertStatus(422);
});

test('can update tipe pembayaran with auto cleanup', function () {
    // Create a test record first
    $tipe = TipePembayaran::create([
        'nama_tipe' => 'TEST_UPDATE_ORIGINAL_' . time(),
        'nominal' => 300000,
        'tipe_periodik' => 'bulanan',
        'keterangan' => 'Will be updated then deleted'
    ]);

    $uniqueName = 'TEST_UPDATE_MODIFIED_' . time();

    $response = $this->putJson("/api/updatetipepembayaran/{$tipe->id_tipe_pembayaran}", [
        'nama_tipe' => $uniqueName,
        'nominal' => 600000,
        'tipe_periodik' => 'semester',
        'keterangan' => 'Updated description'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tipe Pembayaran updated successfully',
            'data' => [
                'nama_tipe' => $uniqueName,
                'nominal' => 600000,
                'tipe_periodik' => 'semester'
            ]
        ]);

    // Cleanup: Delete the test record
    TipePembayaran::where('id_tipe_pembayaran', $tipe->id_tipe_pembayaran)->delete();
});

test('update tipe pembayaran fails for non-existent record', function () {
    $response = $this->putJson('/api/updatetipepembayaran/99999999', [
        'nama_tipe' => 'Test',
        'nominal' => 500000,
        'tipe_periodik' => 'bulanan'
    ]);

    $response->assertStatus(404);
});

test('can delete tipe pembayaran with safe test data', function () {
    // Create a test record specifically for deletion
    $tipe = TipePembayaran::create([
        'nama_tipe' => 'TEST_DELETE_' . time(),
        'nominal' => 100000,
        'tipe_periodik' => 'sekali',
        'keterangan' => 'Created only to be deleted'
    ]);

    $response = $this->deleteJson("/api/deletetipepembayaran/{$tipe->id_tipe_pembayaran}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Tipe Pembayaran deleted successfully']);

    // Verify it's actually deleted
    expect(TipePembayaran::find($tipe->id_tipe_pembayaran))->toBeNull();
});

test('delete tipe pembayaran fails for non-existent record', function () {
    $response = $this->deleteJson('/api/deletetipepembayaran/99999999');

    $response->assertStatus(404);
});

// ==================== Cleanup after all tests ====================

afterAll(function () {
    cleanupTestData();
});
