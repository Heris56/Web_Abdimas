<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class controllerSiswa extends Controller
{
    public function getHistorySiswa($nisn)
    {
        // Ambil riwayat presensi siswa berdasarkan NISN, termasuk catatan dari tabel absen
        $history = DB::table('history_siswa')
            ->join('absen', 'history_siswa.id_absen', '=', 'absen.id_absen')
            ->where('history_siswa.nisn', $nisn)
            ->select(
                'history_siswa.semester',
                'absen.tanggal',
                'absen.keterangan',
                'absen.catatan' // catatan diambil dari absen
            )
            ->orderBy('history_siswa.semester')
            ->orderBy('absen.tanggal')
            ->get()
            ->groupBy('semester');

        // Ambil data siswa
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();

        return view('info-presensi-siswa', [
            'presensiBySemester' => $history,
            'siswa' => $siswa
        ]);
    }

    public function showPresensi()
    {
        // Get the authenticated student's NISN
        $nisn = Auth::id();

        $presensi = DB::table('absen')
            ->where('nisn', $nisn)
            ->orderBy('semester')
            ->orderBy('tanggal')
            ->get()
            ->groupBy('semester');

        // Get student information
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();

        return view('info-presensi-siswa', [
            'presensiBySemester' => $presensi,
            'siswa' => $siswa
        ]);
    }
}