<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StaffKeuangan;

class AuthTest extends TestCase
{
    // Helper to get or create a test staff
    private function getTestStaff(): StaffKeuangan
    {
        $staff = StaffKeuangan::where('email', 'test_only@example.com')->first();
        if (!$staff) {
            $staff = StaffKeuangan::create([
                ['email' => 'test_only@example.com'],
                [
                    'password' => bcrypt('new1234'),
                    'nama' => 'Test Staff',
                    'status' => 'Aktif'
                ]
            ]);
        }
        return $staff;
    }

    /** @test */
    public function staff_can_login_with_valid_credentials()
    {
        $staff = $this->getTestStaff();
        $response = $this->postJson('/api/loginstaff', [
            'email' => $staff->email,
            'password' => 'newtest1234'
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'Login berhasil']);
    }

    /** @test */
    public function staff_cannot_login_with_invalid_email()
    {
        $response = $this->postJson('/api/loginstaff', [
            'email' => 'nonexistent@example.com',
            'password' => 'newtest1234'
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'email atau password salah']);
    }

    /** @test */
    public function staff_cannot_login_with_invalid_password()
    {
        $staff = $this->getTestStaff();
        $response = $this->postJson('/api/loginstaff', [
            'email' => $staff->email,
            'password' => 'wrongpassword'
        ]);
        $response->assertStatus(200)->assertJson(['message' => 'email atau password salah']);
    }

    /** @test */
    public function authenticated_staff_can_logout()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;
        $response = $this->withToken($token)->postJson('/api/logoutstaff');
        $response->assertStatus(200)->assertJson(['message' => 'Logout berhasil']);
    }

    /** @test */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/logoutstaff');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_staff_can_change_password()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/passchangestaff', [
            'new_password' => 'newtest12345',
            'new_password_confirmation' => 'newtest12345'
        ]);
        $staff->update(['password' => bcrypt('newtest1234')]);
        $response->assertStatus(200)->assertJson(['message' => 'Password Berhasil Diganti']);
    }

    /** @test */
    public function change_password_fails_with_mismatched_confirmation()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/passchangestaff', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword'
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function change_password_fails_with_short_password()
    {
        $staff = $this->getTestStaff();
        $token = $staff->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/passchangestaff', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short'
        ]);
        $response->assertStatus(422);
    }
}
