<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class dashboard_wali_kelas_controller extends Controller
{
    public function get_wali_kelas_by_nip(){
        $nip = session('userID');
        $data_absen = $this->get_absen_by_nip($nip);
        $data = DB::table('wali_kelas')->where('nip_wali_kelas',$nip)->get();
        return view('dashboard-wali-kelas', [
            'data_wali_kelas' => $data,
            'data_absen'=> $data_absen,
            'tanggal_list' => $data_absen->pluck('tanggal')->unique()->sort()->values(),
            'list_siswa' => $data_absen->unique('nisn_siswa')
        ]);
    }

    public function get_absen_by_nip($nip)
    {
    $nip = session('userID');

    $data_absen = DB::table('absen')
        ->join('siswa', 'absen.nisn', '=', 'siswa.nisn')
        ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
        ->where('wali_kelas.nip_wali_kelas', $nip)
        ->select('absen.*', 'siswa.nama_siswa as nama_siswa', 'wali_kelas.id_kelas','siswa.nisn as nisn_siswa')
        ->get();

    return $data_absen;
    }

    public function add_tanggal(Request $request)
    {
    $nip = session('userID');
    $data_siswa = $this->get_absen_by_nip($nip);

    foreach ($data_siswa as $siswa) {
        $insertData[] =
        [
        'tanggal' => $request->input('tanggal'),
        'nisn' => $siswa->nisn_siswa,
        'keterangan_absen' => '-',
        ];
    }

    DB::table('absen')->insert($insertData);
    return redirect()->back()->with('success', 'Data absen berhasil ditambahkan.');
    }
}
