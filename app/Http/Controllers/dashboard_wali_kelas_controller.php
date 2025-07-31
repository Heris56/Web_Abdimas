<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class dashboard_wali_kelas_controller extends Controller
{
    public function get_wali_kelas_by_nip(){
        $nip = session('userID');
        $data_absen = $this->get_absen_by_nip($nip);
        $data_siswa = $this->dataSiswa();
        $data = DB::table('wali_kelas')->where('nip_wali_kelas',$nip)->get();
        return view('dashboard-wali-kelas', [
            'data_wali_kelas' => $data,
            'data_absen'=> $data_absen,
            'tanggal_list' => $data_absen->pluck('tanggal')->filter()->unique()->sort()->values(),
            'list_siswa' => $data_siswa->unique('nisn_siswa')
        ]);
    }

    public function get_absen_by_nip($nip)
    {
    $nip = session('userID');
    $tahun_ajaran = $this->getCurrentTahunAjaran();

    $data_absen = DB::table('siswa')
    ->leftJoin('absen', 'absen.nisn', '=', 'siswa.nisn')
    ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
    ->where('wali_kelas.nip_wali_kelas', $nip)
    ->where('siswa.status','aktif')
    ->where('absen.tahun_ajaran', $tahun_ajaran)
    ->select(
        'absen.*',
        'siswa.nama_siswa as nama_siswa',
        'wali_kelas.id_kelas',
        'siswa.nisn as nisn_siswa'
    )
    ->get();

    if($data_absen->isEmpty()){
        $data_absen = DB::table('siswa')
        ->leftJoin('absen', 'absen.nisn', '=', 'siswa.nisn')
    ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
    ->where('wali_kelas.nip_wali_kelas', $nip)
    ->where('siswa.status','aktif')
    ->select(
        'siswa.nama_siswa as nama_siswa',
        'wali_kelas.id_kelas',
        'siswa.nisn as nisn_siswa'
    )
    ->get();
        };

    return $data_absen;
    }

    public function dataSiswa(){
            
        $data_siswa = DB::table('siswa')
            ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
            ->where('wali_kelas.nip_wali_kelas', session('userID'))
            ->where('siswa.status', 'aktif')
            ->select('siswa.nisn as nisn_siswa', 'siswa.nama_siswa as nama_siswa')
            ->get();
        
        return $data_siswa;
    }

    public function getCurrentTahunAjaran()
    {
        $tahun_ajaran = DB::table('tahun_ajaran')
            ->where('is_current', true)
            ->value('tahun');

        return $tahun_ajaran;
    }

    public function add_tanggal(Request $request)
    {
    $nip = session('userID');
    
    $tanggal_input = $request->input('tanggal');
    $tahun_ajaran = $this->getCurrentTahunAjaran();

    $cek_ada = DB::table('absen')
    ->join('siswa', 'absen.nisn', '=', 'siswa.nisn')
    ->join('wali_kelas', 'siswa.id_kelas', '=', 'wali_kelas.id_kelas')
    ->where('wali_kelas.nip_wali_kelas', $nip)
    ->where('absen.tanggal', $tanggal_input)
    ->where('absen.tahun_ajaran', $tahun_ajaran)
    ->exists();
    
    if ($cek_ada) {
        return redirect()->back()->with('error', 'Tanggal Absen sudah ada.');
    }else {
        $data_siswa = $this->get_absen_by_nip($nip)->unique('nisn_siswa');

        if ($data_siswa->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang ditemukan.');
        }
        
        foreach ($data_siswa as $siswa) {
            $insertData[] =
            [
            'tanggal' => $request->input('tanggal'),
            'nisn' => $siswa->nisn_siswa,
            'keterangan_absen' => 'Hadir',
            'tahun_ajaran' => $tahun_ajaran,
            ];
        }
        DB::table('absen')->insert($insertData);
        return redirect()->back()->with('success', 'Tanggal Absen berhasil ditambahkan.');
    }
    }

    public function edit_kehadiran(Request $request)
    {
    
    $data = $request->validate([
    'nisn' => 'required', 
    'tanggal' => 'required|date', 
    'keterangan_absen' => 'nullable|string',
    'tahun_ajaran' => 'required|string'
    ]);

    DB::table('absen')->updateOrInsert(['nisn'=>$data['nisn'],'tanggal'=>$data['tanggal']], ['keterangan_absen' => $data['keterangan_absen'], 'tahun_ajaran'=>$data['tahun_ajaran']]);

    return response()->json(['success' => true, 'message' => 'Data kehadiran berhasil diperbarui.']);
    }


    public function delete_tanggal(Request $request, $tanggal)
    {
        try{
            DB::table('absen')->whereDate('tanggal', $tanggal)->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        }catch(\Exception $e){
            Log::error("Gagal hapus tanggal: " . $e->getMessage());
            return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus data.',
            'error' => $e->getMessage()
        ], 500);
        }
    }

    public function formGantiPassword()
    {
        return view('ganti-password-waliKelas');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        $nip = session('userID');

        $ceknip = DB::table('wali_kelas')->where('nip_wali_kelas', $nip)->exists();

        if (!$ceknip) {
            return redirect()->back()->with('error', 'NIP tidak ditemukan.');
        }else{
            $hashedPassword = Hash::make($request->input('new_password'));
            DB::table('wali_kelas')->where('nip_wali_kelas', $nip)->update(['password' => $hashedPassword, 'pwd_is_changed' => true]);
            return redirect()->route('dashboard-wali-kelas')->with('success', 'Password berhasil diubah.');
        }
    }
}
