<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $nisn = $request->input('nisn_guest') ?? session('userID');

        if (!$nisn) {
            return redirect()->route('login-siswa')->with('error', 'Please login to view attendance data.');
        }

        $isGuest = $request->boolean('isGuest', false);

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

        // Ambil data presensi
        $data_absen = DB::table('absen')
            ->join('siswa', 'absen.nisn', '=', 'siswa.nisn')
            ->where('siswa.nisn', $nisn)
            ->select('absen.*', 'siswa.nama_siswa', 'siswa.tahun_ajaran')
            ->orderBy('absen.tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                $tanggal = \Carbon\Carbon::parse($item->tanggal);
                $bulan = $tanggal->month;
                $tahun = $tanggal->year;

                $item->semester = ($bulan >= 1 && $bulan <= 6) ? '2' : '1'; // Genap = 2, Ganjil = 1
    
                // Tahun ajaran berdasarkan bulan
                $tahun_awal = ($bulan >= 7) ? $tahun : $tahun - 1;
                $tahun_akhir = $tahun_awal + 1;
                $item->tahun_ajaran_label = "$tahun_awal/$tahun_akhir - {$item->semester}";

                return $item;
            });

        // Filter jika ada input tahun_ajaran_label
        $tahunSemesterFilter = $request->input('tahun_ajaran');
        if ($tahunSemesterFilter && $tahunSemesterFilter !== 'all') {
            $data_absen = $data_absen->filter(function ($item) use ($tahunSemesterFilter) {
                return $item->tahun_ajaran_label === $tahunSemesterFilter;
            })->values();
        }

        // Ambil daftar unik tahun ajaran + semester
        $tahunSemesterList = $data_absen->pluck('tahun_ajaran_label')->unique()->sort()->values();

        return view('info-presensi-siswa', [
            'presensi' => $data_absen,
            'siswa' => $siswa,
            'tahunAjaranList' => $tahunSemesterList,
            'tahunAjaranFilter' => $tahunSemesterFilter ?? 'all',
            'isGuest' => $isGuest
        ]);
    }



    public function fetchNilaiSiswa(Request $request)
    {
        try {
            $nisn = $request->input('nisn_guest') ?? session('userID');

            if (!$nisn) {
                return redirect()->route('login-siswa')->with('error', 'Session expired. Please login again.');
            }

            $isGuest = $request->boolean('isGuest', false);

            // Get student information
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
                return redirect()->route('cari')->with('error', 'Student data not found.');
            }

            // Daftar gabungan tahun pelajaran dan semester (mis. "2024/2025 - 1")
            $semesterList = DB::table('nilai')
                ->where('nisn', $nisn)
                ->select(DB::raw("CONCAT(tahun_pelajaran, ' - ', CASE WHEN semester = 'Ganjil' THEN '1' ELSE '2' END) AS tahun_semester"))
                ->distinct()
                ->orderBy('tahun_semester', 'desc')
                ->pluck('tahun_semester');

            $tahunSemesterFilter = $request->input('tahun_ajaran'); // input: "2024/2025 - 1"
            $tahunAjaranFilter = null;
            $semesterFilter = null;

            if ($tahunSemesterFilter && $tahunSemesterFilter !== 'all') {
                [$tahunAjaranFilter, $semesterAngka] = explode(' - ', $tahunSemesterFilter);
                $semesterFilter = $semesterAngka === '1' ? 'Ganjil' : 'Genap';
            }

            // Ambil data nilai
            $data_nilai = DB::table('nilai')
                ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
                ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
                ->where('nilai.nisn', $nisn)
                ->whereNotNull('mapel.nama_mapel')
                ->when($tahunAjaranFilter, function ($query) use ($tahunAjaranFilter) {
                    return $query->where('nilai.tahun_pelajaran', $tahunAjaranFilter);
                })
                ->when($semesterFilter, function ($query) use ($semesterFilter) {
                    return $query->where('nilai.semester', $semesterFilter);
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

            // Group nilai by mapel
            $nilaiByMapel = $data_nilai->groupBy('nama_mapel')->map(function ($grades) {
                $guruMapel = $grades->first()->nama_guru ?? 'Tidak diketahui';
                $tahunPelajaran = $grades->first()->tahun_pelajaran ?? null;
                $semester = $grades->first()->semester ?? null;

                // Konversi semester ke angka (asumsi semester string "Ganjil"/"Genap")
                $semesterAngka = null;
                if ($semester === 'Ganjil') {
                    $semesterAngka = '1';
                } elseif ($semester === 'Genap') {
                    $semesterAngka = '2';
                }

                // Gabungkan tahun ajaran dan semester
                $tahunPelajaranFull = $tahunPelajaran;
                if ($tahunPelajaran && $semesterAngka) {
                    $tahunPelajaranFull = $tahunPelajaran . ' - ' . $semesterAngka;
                }
                return [
                    'grades' => $grades,
                    'guru_mapel' => $guruMapel,
                    'tahun_pelajaran' => $tahunPelajaranFull,
                ];
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'nilai' => $data_nilai,
                        'nilaiByMapel' => $nilaiByMapel,
                        'siswa' => $siswa,
                        'semesterList' => $semesterList,
                        'tahunAjaranFilter' => $tahunSemesterFilter,
                        'isGuest' => $isGuest,
                    ]
                ]);
            }

            return view('info-nilai-siswa', [
                'nilai' => $data_nilai,
                'nilaiByMapel' => $nilaiByMapel,
                'siswa' => $siswa,
                'semesterList' => $semesterList,
                'tahunAjaranFilter' => $tahunSemesterFilter,
                'isGuest' => $isGuest,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchNilaiSiswa', [
                'nisn' => $nisn ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return $request->ajax()
                ? response()->json(['error' => 'Terjadi kesalahan saat memuat nilai.'], 500)
                : redirect()->back()->with('error', 'Terjadi kesalahan saat memuat nilai.');
        }
    }


    public function formGantiPassword()
    {
        return view('ganti-password-siswa');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:6',
        ]);

        $nisn = session('userID');

        // Pastikan NISN ada
        $siswa = DB::table('siswa')->where('nisn', $nisn)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Update password ke hash bcrypt
        DB::table('siswa')
            ->where('nisn', $nisn)
            ->update([
                'password' => Hash::make($request->new_password),
                'pwd_is_changed' => 1
            ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}