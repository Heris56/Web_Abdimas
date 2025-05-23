<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function showPresensi(Request $request)
    {
        $nisn = session('userID');

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

        // Dapatkan daftar tahun ajaran yang tersedia
        $tahunAjaranList = DB::table('siswa')
            ->where('nisn', $nisn)
            ->select('tahun_ajaran')
            ->distinct()
            ->pluck('tahun_ajaran');

        // Filter tahun ajaran dari request atau gunakan tahun ajaran siswa saat ini
        $tahunAjaranFilter = $request->input('tahun_ajaran', $siswa->tahun_ajaran);

        // Ambil data presensi dengan filter
        $data_absen = DB::table('absen')
            ->join('siswa', 'absen.nisn', '=', 'siswa.nisn')
            ->where('siswa.nisn', $nisn)
            ->when($tahunAjaranFilter && $tahunAjaranFilter !== 'all', function ($query) use ($tahunAjaranFilter) {
                return $query->where('siswa.tahun_ajaran', $tahunAjaranFilter);
            })
            ->select('absen.*', 'siswa.nama_siswa as nama_siswa')
            ->orderBy('absen.tanggal', 'desc')
            ->get();

        return view('info-presensi-siswa', [
            'presensi' => $data_absen,
            'siswa' => $siswa,
            'tahunAjaranList' => $tahunAjaranList,
            'tahunAjaranFilter' => $tahunAjaranFilter
        ]);
    }




    public function fetchNilaiSiswa(Request $request)
    {
        try {
            $nisn = session('userID');

            if (!$nisn) {
                Log::warning('fetchNilaiSiswa: No NISN found in session');
                if ($request->ajax()) {
                    return response()->json(['error' => 'Session expired'], 401);
                }
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }

            Log::info('Fetching nilai for student', ['nisn' => $nisn]);

            // Get student information
            $siswa = DB::table('siswa')
                ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
                ->where('siswa.nisn', $nisn)
                ->select(
                    'siswa.nisn',
                    'siswa.nama_siswa',
                    'siswa.tahun_ajaran',
                    'kelas.id_kelas',
                    'kelas.jurusan',
                )
                ->first();

            if (!$siswa) {
                Log::warning('Student not found', ['nisn' => $nisn]);
                if ($request->ajax()) {
                    return response()->json(['error' => 'Student data not found'], 404);
                }
                return redirect()->back()->with('error', 'Student data not found.');
            }

            // Get list of available academic years
            $tahunAjaranList = DB::table('nilai')
                ->where('nisn', $nisn)
                ->select('tahun_pelajaran')
                ->distinct()
                ->orderBy('tahun_pelajaran', 'desc')
                ->pluck('tahun_pelajaran');

            // Get selected academic year from request or use student's default
            $tahunAjaranFilter = $request->input('tahun_ajaran');
            

            // Get student grades with filters
            $data_nilai = DB::table('nilai')
                ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
                ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
                ->where('nilai.nisn', $nisn)
                ->whereNotNull('mapel.nama_mapel')
                ->when($tahunAjaranFilter && $tahunAjaranFilter !== 'all', function ($query) use ($tahunAjaranFilter) {
                    // Filter berdasarkan tahun pelajaran yang tepat
                    return $query->where('nilai.tahun_pelajaran', $tahunAjaranFilter);
                })
                ->select(
                    'nilai.id_nilai', 
                    'mapel.nama_mapel',
                    'mapel.id_mapel',
                    'nilai.kegiatan',
                    'nilai.nilai',
                    'nilai.tanggal',
                    'nilai.tahun_pelajaran',
                    'nilai.semester', 
                    'guru_mapel.nama_guru'
                )
                ->orderBy('nilai.tahun_pelajaran', 'desc')
                ->orderBy('mapel.nama_mapel')
                ->orderBy('nilai.tanggal', 'desc')
                ->get();
            Log::info('Student data fetched successfully', [
                'nisn' => $nisn,
                'total_grades' => $data_nilai->count()
            ]);

            // Group grades by subject
            $nilaiByMapel = $data_nilai->groupBy('nama_mapel')->map(function ($grades) {
                $gradeValues = $grades->pluck('nilai')->filter()->values();
                return [
                    'grades' => $grades,
                    'average' => $gradeValues->count() > 0 ? round($gradeValues->avg(), 2) : 0,
                    'highest' => $gradeValues->count() > 0 ? $gradeValues->max() : 0,
                    'lowest' => $gradeValues->count() > 0 ? $gradeValues->min() : 0,
                    'total_assignments' => $grades->count()
                ];
            });

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'nilai' => $data_nilai,
                        'nilaiByMapel' => $nilaiByMapel,
                        'siswa' => $siswa,
                        'tahunAjaranList' => $tahunAjaranList,
                        'tahunAjaranFilter' => $tahunAjaranFilter
                    ]
                ]);
            }

            return view('info-nilai-siswa', [
                'nilai' => $data_nilai,
                'nilaiByMapel' => $nilaiByMapel,
                'siswa' => $siswa,
                'tahunAjaranList' => $tahunAjaranList,
                'tahunAjaranFilter' => $tahunAjaranFilter
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchNilaiSiswa', [
                'nisn' => $nisn ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to fetch student grades. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to load student grades. Please try again.');
        }
    }

}