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
            'data_absen'=> $data_absen
        ]);
    }

    public function get_absen_by_nip($nip)
    {
    $nip = session('userID');

    $data_absen = DB::table('absen')
        ->join('siswa', 'absen.nisn', '=', 'siswa.nisn')
        ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
        ->where('wali_kelas.nip_wali_kelas', $nip)
        ->select('absen.*', 'siswa.nama_siswa as nama_siswa', 'wali_kelas.id_kelas')
        ->get();

    return $data_absen;
    }
}
