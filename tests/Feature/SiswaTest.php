<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Siswa;

class SiswaTest extends TestCase
{
    /** @test */
    public function can_fetch_siswa_list()
    {
        $response = $this->getJson('/api/getsiswa');
        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'data' => [['nisn', 'nama_siswa', 'status']],
            'meta'
        ]);
    }

    /** @test */
    public function can_search_siswa_by_name()
    {
        $response = $this->getJson('/api/getsiswa?search=a');
        $response->assertStatus(200)->assertJsonStructure(['message', 'data', 'meta']);
    }

    /** @test */
    public function can_search_siswa_by_nisn()
    {
        $siswa = Siswa::first();
        if ($siswa) {
            $response = $this->getJson("/api/getsiswa?search={$siswa->nisn}");
            $response->assertStatus(200)->assertJsonFragment(['nisn' => $siswa->nisn]);
        } else {
            $this->markTestSkipped('No siswa data available');
        }
    }

    /** @test */
    public function can_filter_siswa_by_status()
    {
        $response = $this->getJson('/api/getsiswa?status=Aktif');
        $response->assertStatus(200)->assertJsonStructure(['message', 'data', 'meta']);
    }

    /** @test */
    public function can_paginate_siswa_results()
    {
        $response = $this->getJson('/api/getsiswa?per_page=5');
        $response->assertStatus(200)->assertJsonPath('meta.per_page', 5);
    }
}
