<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ControllerSiswa extends Controller
{
    public function getHistorySiswa()
    {
        $nisn = session('userId'); // Menggunakan session userID seperti di NilaiController

        // Ambil riwayat presensi siswa
        $history = DB::table('history_siswa')
            ->join('absen', 'history_siswa.id_absen', '=', 'absen.id_absen')
            ->where('history_siswa.nisn', $nisn)
            ->select(
                'history_siswa.semester',
                'history_siswa.tahun_ajaran',
                'absen.tanggal',
                'absen.keterangan',
                'absen.catatan'
            )
            ->orderBy('history_siswa.semester')
            ->orderBy('absen.tanggal')
            ->get()
            ->groupBy('semester');

        // Ambil data siswa
        $siswa = DB::table('siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->where('siswa.nisn', $nisn)
            ->select(
                'siswa.nisn',
                'siswa.nama_siswa',
                'siswa.tahun_ajaran',
                'kelas.id_kelas',
                'kelas.jurusan'
            )
            ->first();

        return view('info-presensi-siswa', [
            'presensiBySemester' => $history,
            'siswa' => $siswa
        ]);
    }

    public function showPresensi()
    {
        $nisn = session('userID'); // Menggunakan session userID

        // Ambil data presensi + tambahkan catatan
        $presensi = DB::table('history_siswa')
            ->join('absen', 'history_siswa.id_absen', '=', 'absen.id_absen')
            ->where('history_siswa.nisn', $nisn)
            ->select(
                'history_siswa.semester',
                'absen.tanggal',
                'absen.keterangan_absen',
                'absen.id_absen'
            )
            ->orderBy('history_siswa.semester')
            ->orderBy('absen.tanggal')
            ->get()
            ->groupBy('semester');

        // Ambil data siswa
        $siswa = DB::table('siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->where('siswa.nisn', $nisn)
            ->select(
                'siswa.nisn',
                'siswa.nama_siswa',
                'siswa.tahun_ajaran',
                'kelas.id_kelas',
                'kelas.jurusan'
            )
            ->first();

        return view('info-presensi-siswa', [
            'presensiBySemester' => $presensi,
            'siswa' => $siswa
        ]);
    }

}