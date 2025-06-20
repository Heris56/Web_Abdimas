<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ControllerSiswa extends Controller
{
    public function getHistorySiswa()
    {
        // This method seems to be an older version or not fully integrated.
        // The showPresensi method is more comprehensive.
        // Consider deprecating or integrating its logic into showPresensi.
        $nisn = session('userId');

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
            'siswa' => $siswa,
            'isGuest' => false // Explicitly set for authenticated route if this method is still used
        ]);
    }

    public function showGuestInfoSiswa(Request $request)
    {
        $request->validate([
            'inputNISN' => 'required|numeric',
        ]);

        $nisn = $request->input('inputNISN');

        $siswaExists = DB::table('siswa')->where('nisn', $nisn)->exists();
        if (!$siswaExists) {
            return redirect()->route('cari')->with('error', 'NISN tidak ditemukan.');
        }

        // Determine which tab to show (presensi or nilai)
        $tab = $request->input('tab', 'presensi'); // Default to presensi

        if ($tab === 'nilai') {
            return $this->fetchNilaiSiswa($request->merge(['nisn_guest' => $nisn, 'isGuest' => true]));
        }

        return $this->showPresensi($request->merge(['nisn_guest' => $nisn, 'isGuest' => true]));
    }


    public function showPresensi(Request $request)
    {
        $nisn = $request->input('nisn_guest') ?? session('userID'); // Prioritize guest NISN if available

        // If no NISN is found (neither from guest nor session), redirect to login
        if (!$nisn) {
            return redirect()->route('login-siswa')->with('error', 'Please login to view attendance data.');
        }

        $isGuest = $request->boolean('isGuest', false); // Get the isGuest flag

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

        if (!$siswa) {
            return redirect()->route('cari')->with('error', 'Student data not found for the provided NISN.');
        }

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
            'tahunAjaranFilter' => $tahunAjaranFilter,
            'isGuest' => $isGuest // Pass the isGuest flag to the view
        ]);
    }


    public function fetchNilaiSiswa(Request $request)
    {
        try {
            $nisn = $request->input('nisn_guest') ?? session('userID'); // Prioritize guest NISN if available

            // If no NISN is found (neither from guest nor session), redirect to login
            if (!$nisn) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Session expired'], 401);
                }
                return redirect()->route('login-siswa')->with('error', 'Session expired. Please login again.');
            }

            $isGuest = $request->boolean('isGuest', false); // Get the isGuest flag

            Log::info('Fetching nilai for student', ['nisn' => $nisn, 'isGuest' => $isGuest]);

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
                return redirect()->route('cari')->with('error', 'Student data not found.');
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
                $guruMapel = $grades->first()->nama_guru ?? 'Tidak diketahui';
                return [
                    'grades' => $grades,
                    'guru_mapel' => $guruMapel,
                    'tahun_pelajaran' => $grades->first()->tahun_pelajaran,
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
                        'tahunAjaranFilter' => $tahunAjaranFilter,
                    ]
                ]);
            }

            return view('info-nilai-siswa', [
                'nilai' => $data_nilai,
                'nilaiByMapel' => $nilaiByMapel,
                'siswa' => $siswa,
                'tahunAjaranList' => $tahunAjaranList,
                'tahunAjaranFilter' => $tahunAjaranFilter,
                'isGuest' => $isGuest // Pass the isGuest flag to the view
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

    public function formGantiPassword()
    {
        // This method should only be accessible by logged-in users.
        // It's already protected by middleware.
        return view('ganti-password-siswa');
    }

    public function updatePassword(Request $request)
    {
        // This method should only be accessible by logged-in users.
        // It's already protected by middleware.
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        $siswa = auth()->guard('siswa')->user(); // Adjust if not using guard

        if (!Hash::check($request->current_password, $siswa->password)) {
            return back()->with('error', 'Password lama tidak cocok.');
        }

        $siswa->password = Hash::make($request->new_password);
        $siswa->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}