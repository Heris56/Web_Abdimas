<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class DataController extends Controller
{
    public function fetchData($type)
    {
        switch ($type) {
            case 'siswa':
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'status_tahun_ajaran' => 'Status',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
                break;
            case 'kelas':
                $data = DB::table('kelas')->get();
                $columns = [
                    'id_kelas' => 'Kelas',
                    'jurusan' => 'Jurusan'
                ];
                break;
            case 'guru_mapel':
                $data = DB::table('wali_kelas')->get();
                $columns = [
                    'nip_wali_kelas' => 'NIP',
                    'nama' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status_tahun_ajaran' => 'Status',
                    'id_kelas' => 'Kelas'
                ];
                break;
            case 'wali_kelas':
                $data = DB::table('guru_mapel')->get();
                $columns = [
                    'nip_guru_mapel' => 'NIP',
                    'nama_guru' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status_tahun_ajaran' => 'Status',
                    'id_mapel' => 'ID Mapel',
                    'id_kelas' => 'Kelas'
                ];
                break;
            case 'mapel':
                $data = DB::table('mapel')->get();
                $columns = [
                    'id_mapel' => 'ID Mapel',
                    'nama_mapel' => 'Nama Mapel'
                ];
                break;
            default:
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'status_tahun_ajaran' => 'Status',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
        }

        return view('dashboard-staff', [
            'data' => $data,
            'columns' => $columns,
            'type' => $type
        ]);
    }
}
