<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// TODO::
// bugged pas masukin nilai ke siswa yang semesternya kosong
// fix kegiatan fetching to limit only kegiatan that belongs to that mapel

class NilaiController extends Controller
{
    public function fetchNilai(Request $request)
    {
        // 198001012005011001
        // pwd123
        $nip = session('userID');
        Log::info('NIP', ['nip' => $nip]);

        // untuk tabs mapel
        $mapelList = $this->getListMapelByNipGuruMapel($nip);
        $mapelIds = array_keys($mapelList);
        Log::info('mapelList', ['mapelList' => $mapelList]);

        // untuk filter kelas
        $kelasList = $this->getListKelasByMapel($nip);
        Log::info('kelasList', ['kelasList' => $kelasList]);

        // untuk buat column sesuai kegiatan
        $kegiatanList = $this->getListKegiatanByMapel();
        Log::info('kegiatanList', ['kegiatanList' => $kegiatanList]);

        $siswaList = $this->getAllSiswa();
        Log::info('siswaList', ['siswaList' => $siswaList]);

        $nilaiList = $this->getNilaiList($mapelIds);
        Log::info('nilaiList', ['nilaiList' => $nilaiList]);

        $tahunAjaran = $this->getTahunAjaranAktif();
        $semester = $this->getSemesterAktif();

        // build main query
        $data = [];
        foreach ($mapelList as $idMapel => $namaMapel) {
            foreach ($kelasList[$idMapel] ?? [] as $kelas) {
                $siswaKelas = $siswaList[$kelas] ?? collect();
                $kegiatan = $kegiatanList[$idMapel] ?? [];

                foreach ($siswaKelas as $siswa) {
                    $nilaiKey = $siswa->nisn . '|' . $idMapel;
                    $nilai = $nilaiList[$nilaiKey] ?? collect();

                    $data[$idMapel]['nama_mapel'] = $namaMapel;
                    $data[$idMapel]['kelas'][$kelas]['kegiatan'] = $kegiatan;
                    $data[$idMapel]['kelas'][$kelas]['siswa'][] = [
                        'nisn' => $siswa->nisn,
                        'nama_siswa' => $siswa->nama_siswa,
                        'nilai' => $nilai->toArray(),
                    ];
                }
            }
        }
        dd($data, $kegiatanList, $mapelList,  $siswaList, $tahunAjaran, $semester, $nilaiList, $kelasList);

        return view('dashboard-guru-mapel', [
            'data' => $data,
            'kegiatanList' => $kegiatanList,
            'mapelList' => $mapelList,
            'kelasList' => $kelasList,
            'nilaiList' => $nilaiList,
            'siswaList' => $siswaList,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester
        ]);
    }

    public function inputNilai(Request $request)
    {
        try {
            $nip = session('userID');

            Log::info('inputNilai called', [
                'nip' => $nip,
                'nisn' => $request->input('nisn'),
                'kegiatan' => $request->input('kegiatan'),
                'nilai' => $request->input('nilai'),
                'mapel' => $request->input('mapel'),
                'tahun_pelajaran' => $request->input('tahun_pelajaran'),
                'id_kelas' => $request->input('id_kelas')
            ]);

            // Validate input
            $validated = $request->validate([
                'nisn' => 'required|exists:siswa,nisn',
                'kegiatan' => 'required|string',
                'nilai' => 'required|numeric|min:0|max:100',
                'mapel' => 'required|exists:mapel,nama_mapel',
                'tahun_pelajaran' => 'required|string',
                'id_kelas' => 'nullable|string|exists:siswa,id_kelas'
            ]);

            // Get id_mapel from mapel name
            $id_mapel = DB::table('mapel')
                ->where('nama_mapel', $validated['mapel'])
                ->value('id_mapel');

            if (!$id_mapel) {
                Log::error('Mapel not found', ['mapel' => $validated['mapel']]);
                return response()->json(['message' => 'Mapel tidak ditemukan'], 404);
            }

            // Verify teacher has access to this mapel
            $guru_mapel = DB::table('guru_mapel')
                ->where('nip_guru_mapel', $nip)
                ->where('id_mapel', $id_mapel)
                ->exists();

            if (!$guru_mapel) {
                Log::error('Unauthorized access to mapel', ['nip' => $nip, 'id_mapel', 'id_kelas' => 'id_kelas']);
                return response()->json(['message' => 'Anda tidak memiliki akses ke mapel ini'], 403);
            }

            // Insert new grades for each kegiatan
            DB::beginTransaction();
            foreach ($request->nilai as $kegiatan => $nilai) {
                if (!is_null($nilai)) {
                    // Check if grade already exists
                    $exists = DB::table('nilai')
                        ->where('nisn', $validated['nisn'])
                        ->where('id_mapel', $validated['id_mapel'])
                        ->where('tahun_pelajaran', $validated['tahun_pelajaran'])
                        ->where('nip_guru_mapel', $nip)
                        ->where('kegiatan', $kegiatan)
                        ->exists();

                    if ($exists) {
                        Log::warning('Grade already exists', [
                            'nisn' => $validated['nisn'],
                            'id_mapel' => $validated['id_mapel'],
                            'tahun_pelajaran' => $validated['tahun_pelajaran'],
                            'kegiatan' => $kegiatan,
                        ]);
                        continue; // Skip if record exists (input only, no update)
                    }

                    // Insert new grade
                    DB::table('nilai')->insert([
                        'nisn' => $validated['nisn'],
                        'id_mapel' => $validated['id_mapel'],
                        'nip_guru_mapel' => $nip,
                        'tahun_pelajaran' => $validated['tahun_pelajaran'],
                        'kegiatan' => $kegiatan,
                        'nilai' => $nilai,
                        'tanggal' => now(),
                    ]);
                }
            }
            DB::commit();

            Log::info('Grades inserted successfully', [
                'nisn' => $validated['nisn'],
                'id_mapel' => $validated['id_mapel'],
                'tahun_pelajaran' => $validated['tahun_pelajaran'],
            ]);

            return response()->json(['message' => 'Nilai berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in inputNilai', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Data tidak valid', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in inputNilai', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan nilai'], 500);
        }
    }

    public function updateNilai(Request $request)
    {
        $nisn = trim((string) $request->input('nisn')); // pastikan keluaran string
        $field = trim($request->input('field'));
        $value = $request->input('value');
        $tahun_pelajaran = $request->input('tahun_pelajaran');
        $semester = $request->input('semester');
        $id_mapel = $request->input('id_mapel');
        $nip_guru_mapel = $request->input('nip_guru_mapel');

        Log::info('updateNilai inputs', [
            'nisn' => $nisn,
            'field' => $field,
            'value' => $value,
            'tahun_pelajaran' => $tahun_pelajaran,
            'semester' => $semester,
            'id_mapel' => $id_mapel,
            'nip' => $nip_guru_mapel,
            'nisn_type' => gettype($nisn),
            'nisn_length' => strlen($nisn),
            'field_type' => gettype($field)
        ]);

        try {
            $request->validate([
                'nisn' => 'required|string',
                'field' => 'required|string',
                'value' => 'nullable|string|min:0|max:100',
                'tahun_pelajaran' => 'required|string',
                'semester' => 'required|in:Ganjil,Genap',
                'id_mapel' => 'required|string',
                'nip_guru_mapel' => 'required|string',
            ]);


            $record = DB::table('nilai')
                ->where('nisn', $nisn)
                ->where('kegiatan', $field)
                ->where('tahun_pelajaran', $tahun_pelajaran)
                ->where('semester', $semester)
                ->where('id_mapel', $id_mapel)
                ->where('nip_guru_mapel', $nip_guru_mapel)
                ->first();

            Log::info('Record query result', [
                'record' => $record,
                'query' => "SELECT * FROM nilai WHERE nisn = '$nisn' AND kegiatan = '$field' AND tahun_pelajaran = '$tahun_pelajaran' AND semester = '$semester' AND id_mapel = '$id_mapel' AND nip_guru_mapel = '$nip_guru_mapel'"
            ]);

            DB::beginTransaction();
            if ($record) {
                // Update existing record
                $updated = DB::table('nilai')
                    ->where('nisn', $nisn)
                    ->where('kegiatan', $field)
                    ->where('tahun_pelajaran', $tahun_pelajaran)
                    ->where('semester', $semester)
                    ->where('id_mapel', $id_mapel)
                    ->where('nip_guru_mapel', $nip_guru_mapel)
                    ->update([
                        'nilai' => $value,
                        'tanggal' => Carbon::now()->format('Y-m-d H:i:s') // or just now()
                    ]);

                Log::info('Update operation', [
                    'updated_rows' => $updated,
                    'tanggal' => now(),
                    'nilai' => $value,
                    'nisn' => $nisn,
                    'kegiatan' => $field,
                    'tahun_pelajaran' => $tahun_pelajaran,
                    'semester' => $semester,
                    'id_mapel' => $id_mapel,
                    'nip_guru_mapel' => $nip_guru_mapel
                ]);

                if ($updated === 0) {
                    DB::rollBack();
                    Log::error('Update failed, no rows affected');
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update nilai: no rows affected'
                    ], 500);
                }

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Berhasil update nilai!']);
            } else {
                // Fetch student data
                $siswa = DB::table('siswa')
                    ->where('nisn', $nisn)
                    ->first();

                if (!$siswa) {
                    DB::rollBack();
                    Log::error('Siswa not found', ['nisn' => $nisn]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Siswa not found for nisn: ' . $nisn
                    ], 404);
                }

                // Insert new record
                $insertData = [
                    'nilai' => $value,
                    'tanggal' => now(),
                    'tahun_pelajaran' => $tahun_pelajaran,
                    'semester' => $semester,
                    'nisn' => $nisn,
                    'nip_guru_mapel' => $nip_guru_mapel,
                    'id_mapel' => $id_mapel,
                    'kegiatan' => $field,
                ];

                $inserted = DB::table('nilai')->insert($insertData);

                Log::info('Insert operation', [
                    'inserted' => $inserted,
                    'data' => $insertData,
                    'query' => "INSERT INTO nilai (" . implode(', ', array_keys($insertData)) . ") VALUES (" . implode(', ', array_map(fn($v) => is_null($v) ? 'NULL' : "'$v'", array_values($insertData))) . ")"
                ]);

                if (!$inserted) {
                    DB::rollBack();
                    Log::error('Insert failed');
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to insert nilai'
                    ], 500);
                }

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Berhasil insert nilai!']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in updateNilai', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambahKegiatan(Request $request)
    {
        try {
            $nip = session('userID');
            $id_mapel = $request->input('mapelSelect');
            $tahunAjaran = $this->getTahunAjaranAktif();
            $kegiatan = $request->input('inputKegiatan');
            $semester = $this->getSemesterAktif();

            Log::info('storeKegiatan called', [
                'nip' => $nip,
                'id_mapel' => $id_mapel,
                'tahunAjaran' => $tahunAjaran,
                'request_data' => $request->all(),
            ]);

            $assigned = $this->verifyTeacherAccessToMapel($nip, $id_mapel);

            if (!$assigned) {
                Log::error('Unauthorized access to mapel', [
                    'nip' => $nip,
                    'id_mapel' => $id_mapel,
                ]);
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke mapel ini');
            }

            $exists = DB::table('nilai')
                ->where('id_mapel', $id_mapel)
                ->where('tahun_pelajaran', $tahunAjaran)
                ->where('kegiatan', $kegiatan)
                ->exists();

            if ($exists) {
                Log::warning('Kegiatan already exists', [
                    'mapel' => $id_mapel,
                    'tahun_pelajaran' => $tahunAjaran,
                    'kegiatan' => $kegiatan,
                ]);
                return redirect()->back()->with('error', 'Kegiatan sudah ada');
            }

            $students = DB::table('siswa')
                ->join('paket_mapel', 'siswa.id_kelas', '=', 'paket_mapel.id_kelas')
                ->join('guru_mapel', function ($join) use ($nip) {
                    $join->on('paket_mapel.kode_paket', '=', 'guru_mapel.kode_paket')
                        ->where('guru_mapel.nip_guru_mapel', '=', $nip);
                })
                ->leftJoin('nilai', function ($join) use ($id_mapel, $tahunAjaran, $semester, $nip, $kegiatan) {
                    $join->on('siswa.nisn', '=', 'nilai.nisn')
                        ->where('nilai.id_mapel', '=', $id_mapel)
                        ->where('nilai.tahun_pelajaran', '=', $tahunAjaran)
                        ->where('nilai.semester', '=', $semester)
                        ->where('nilai.kegiatan', '=', $kegiatan)
                        ->where('nilai.nip_guru_mapel', '=', $nip);
                })
                ->whereNull('nilai.id_nilai')
                ->where('paket_mapel.id_mapel', $id_mapel)
                ->select('siswa.nisn')
                ->distinct()
                ->pluck('nisn');



            if ($students->isEmpty()) {
                Log::warning('No students to insert nilai for this kegiatan', [
                    'kegiatan' => $kegiatan,
                    'id_mapel' => $id_mapel,
                    'tahun_pelajaran' => $tahunAjaran,
                    'nip' => $nip,
                ]);
                return response()->json(['message' => 'Tidak ada siswa yang bisa ditambahkan untuk kegiatan ini'], 400);
            }



            DB::beginTransaction();
            foreach ($students as $nisn) {
                DB::table('nilai')->insert([
                    'nisn' => $nisn,
                    'id_mapel' => $id_mapel,
                    'nip_guru_mapel' => $nip,
                    'tahun_pelajaran' => $tahunAjaran,
                    'kegiatan' => $kegiatan,
                    'semester' => $semester,
                    'nilai' => null,
                ]);
            }
            DB::commit();

            Log::info('Kegiatan inserted successfully', [
                'nip' => $nip,
                'id_mapel' => $id_mapel,
                'tahun_pelajaran' => $tahunAjaran,
                'semester' => $semester,
                'kegiatan' => $kegiatan,
            ]);
            return redirect()->back()->with('success', 'Berhasil menambahkan kegiatan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storeKegiatan', ['errors' => $e->errors()]);
            return redirect()->back()->with('error', 'Gagal menambahkan kegiatan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeKegiatan', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menambahkan kegiatan.');
        }
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        $nip = session('userID');

        $ceknip = DB::table('guru_mapel')->where('nip_guru_mapel', $nip)->exists();

        if (!$ceknip) {
            return redirect()->back()->with('error', 'NIP tidak ditemukan.');
        } else {
            $hashedPassword = Hash::make($request->input('new_password'));
            DB::table('guru_mapel')->where('nip_guru_mapel', $nip)->update(['password' => $hashedPassword, 'pwd_is_changed' => true]);
            return redirect()->route('nilai.fetch')->with('success', 'Password berhasil diubah.');
        }
    }

    public function deleteKegiatan(Request $request)
    {
        try {
            $nip = session('userID');
            $kegiatan = $request->input('kegiatan');
            $id_mapel = $request->input('id_mapel');

            if (!$kegiatan || !$id_mapel || !$nip) {
                return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
            }

            DB::statement('SET SQL_SAFE_UPDATES = 0');
            DB::table('nilai')
                ->where('kegiatan', $kegiatan)
                ->where('id_mapel', $id_mapel)
                ->where('nip_guru_mapel', $nip)
                ->delete();
            DB::statement('SET SQL_SAFE_UPDATES = 1');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Log the error and return friendly message
            Log::error($e);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // returns [ 'MAP01' => 'Biologi', ... ]
    public function getListMapelByNipGuruMapel($nip_guru_mapel)
    {
        $mapelList = DB::table('guru_mapel')
            ->join('paket_mapel', 'guru_mapel.kode_paket', '=', 'paket_mapel.kode_paket')
            ->join('mapel', 'paket_mapel.id_mapel', '=', 'mapel.id_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip_guru_mapel)
            ->select('mapel.id_mapel', 'mapel.nama_mapel')
            ->distinct()
            ->pluck('mapel.nama_mapel', 'mapel.id_mapel') // key = id, value = nama
            ->toArray();

        return $mapelList;
    }

    public function getKegiatanOfEachMapelByNipGuruMapel($nip)
    {
        $tahunAjaran = $this->getTahunAjaranAktif();
        $kegiatanList = DB::table('nilai')
            ->select('id_mapel', 'kegiatan')
            ->where('nip_guru_mapel', $nip)
            ->where('tahun_pelajaran', $tahunAjaran)
            ->distinct()
            ->orderBy('kegiatan')
            ->get()
            ->groupBy('id_mapel')
            ->map(function ($group) {
                return $group->pluck('kegiatan')->unique()->values()->toArray();
            })
            ->toArray();

        Log::info('kegiatanList', ['kegiatanList' => $kegiatanList]);

        return $kegiatanList;
    }

    // returns something like this
    // [
    //     'MAP01' => ['Quiz 1', 'Quiz 2'],
    //     'MAP02' => ['Tugas 1'],
    // ]
    public function getListKegiatanByMapel()
    {
        $nip = session('userID');
        $kegiatanList = DB::table('nilai')
            ->where('nip_guru_mapel', $nip)
            ->select('id_mapel', 'kegiatan')
            ->distinct()
            ->orderBy('kegiatan')
            ->get()
            ->groupBy('id_mapel')
            ->map(function ($items) {
                return $items->pluck('kegiatan')->toArray();
            })
            ->toArray();

        return $kegiatanList;
    }


    public function getListKelasByMapel($nip)
    {
        $mapelList = $this->getListMapelByNipGuruMapel($nip); // returns [ 'MAP01' => 'Biologi', ... ]

        $mapelKelasMap = [];

        foreach (array_keys($mapelList) as $id_mapel) {
            $kelasList = DB::table('guru_mapel')
                ->join('paket_mapel', 'guru_mapel.kode_paket', '=', 'paket_mapel.kode_paket')
                ->join('kelas', 'paket_mapel.id_kelas', '=', 'kelas.id_kelas')
                ->where('guru_mapel.nip_guru_mapel', $nip)
                ->where('paket_mapel.id_mapel', $id_mapel)
                ->select('kelas.id_kelas')
                ->pluck('kelas.id_kelas')
                ->toArray();

            $mapelKelasMap[$id_mapel] = $kelasList;
        }

        return $mapelKelasMap;
    }


    public function getTahunAjaranAktif()
    {
        $tahunAjaran = DB::table('tahun_ajaran')->where('is_current', 1)->value('tahun');

        return $tahunAjaran;
    }

    public function getNilaiList(array $mapelIds)
    {
        return DB::table('nilai')
            ->whereIn('id_mapel', $mapelIds)
            ->select('nisn', 'id_mapel', 'kegiatan', 'nilai')
            ->get()
            ->groupBy(fn($n) => $n->nisn . '|' . $n->id_mapel)
            ->map(function ($group) {
                return collect($group)->pluck('nilai', 'kegiatan');
            });
    }


    public function getSemesterAktif()
    {
        $semester = DB::table('tahun_ajaran')->where('is_current', 1)->value('semester');

        return $semester;
    }

    public function verifyTeacherAccessToMapel($nip, $mapel)
    {
        Log::info('verifyTeacher called', [
            'nip' => $nip,
            'id_mapel' => $mapel,
        ]);
        $assigned = DB::table('guru_mapel')
            ->join('paket_mapel', 'guru_mapel.kode_paket', '=', 'paket_mapel.kode_paket')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->where('paket_mapel.id_mapel', $mapel)
            ->exists();

        return $assigned;
    }

    public function getIdMapel($mapel)
    {
        return DB::table('mapel')
            ->where('nama_mapel', $mapel)
            ->value('id_mapel');
    }

    public function getAllSiswa()
    {
        $siswaPerKelas = DB::table('siswa')
            ->select('id_kelas', 'nisn', 'nama_siswa')
            ->get()
            ->groupBy('id_kelas');

        return $siswaPerKelas;
    }
}
