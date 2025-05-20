<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function fetchNilai()
    {
        $data = DB::table('nilai')->get();
        return view('dashboard-guru-mapel', [
            'data' => $data
        ]);
    }

    public function fetchNilaiForEachGuru()
    {
        // 197806152005011001
        // budi123
        $nip = session('userID');

        // simpan kegiatan (Quiz 1,2, etc) ke list
        // untuk menentukan header di UI dashboard guru mapel
        $kegiatanList = DB::table('nilai')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->toArray();

        $query = DB::table('nilai')
            ->join('siswa', 'nilai.nisn', '=', 'siswa.nisn')
            ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->select(
                'siswa.nisn',
                'siswa.id_kelas',
                'siswa.nama_siswa',
                'mapel.nama_mapel',
                'guru_mapel.nama_guru'
            );

        foreach ($kegiatanList as $kegiatan) {
            $alias = str_replace(' ', '_', strtolower($kegiatan));
            $query->addSelect(DB::raw("MAX(CASE WHEN kegiatan = '$kegiatan' THEN nilai END) as `$alias`"));
        }

        $data_nilai = $query
            ->groupBy(
                'siswa.nisn',
                'siswa.id_kelas',
                'siswa.nama_siswa',
                'mapel.nama_mapel',
                'guru_mapel.nama_guru'
            )->get();

        return view('dashboard-guru-mapel', [
            'data_nilai' => $data_nilai,
            'kegiatanList' => $kegiatanList
        ]);
    }

    public function inputNilai()
    {
        // input nilai logic
    }
}
