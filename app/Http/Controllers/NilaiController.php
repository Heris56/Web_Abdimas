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
        $nip = session('userID');

        // simpan kegiatan (Quiz 1,2, etc) ke list
        // untuk menentukan header di UI dashboard guru mapel
        $kegiatanList = DB::table('nilai')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->toArray();

        $query = DB::table('nilai')
            ->join('siswa', 'nilai.id_siswa', '=', 'siswa.id_siswa')
            ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru_mapel', 'nilai.id_guru_mapel', '=', 'guru_mapel.id_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->select('siswa.nama_siswa', 'mapel.nama_mapel', 'guru_mapel.nama_guru');

        foreach ($kegiatanList as $kegiatan) {
            $alias = str_replace(' ', '_', strtolower($kegiatan));
            $query->addSelect(DB::raw("MAX(CASE WHEN kegiatan = '$kegiatan' THEN nilai END) as `$alias`"));
        }

        $data_nilai = $query
            ->groupBy('siswa.nama_siswa', 'mapel.nama_mapel', 'guru_mapel.nama_guru')
            ->get();

        return view('dashboard-guru-mapel', [
            'data' => $data_nilai,
            'kegiatanList' => $kegiatanList
        ]);
    }
}
