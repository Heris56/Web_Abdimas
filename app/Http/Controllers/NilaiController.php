<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function fetchNilai(Request $request)
    {
        // 197806152005011001
        // budi123
        $nip = session('userID');

        Log::info('NIP', ['nip' => $nip]);

        // untuk filter mapel
        $mapelList = $this->getListMapelByNipGuruMapel($nip);
        Log::info('mapelList', ['mapelList' => $mapelList]);

        $tahunAjaran = $this->getTahunAjaranAktif();

        $semesterList = DB::table('nilai')
            ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->distinct()
            ->pluck('nilai.semester')
            ->toArray();
        Log::info('semesterList', ['semesterList' => $semesterList]);

        // untuk filter tahun ajaran
        $tahunPelajaranList = DB::table('nilai')
            ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->distinct()
            ->pluck('nilai.tahun_pelajaran')
            ->toArray();
        Log::info('tahunPelajaranList', ['tahunPelajaranList' => $tahunPelajaranList]);

        // untuk filter kelas
        $kelasList = DB::table('siswa')
            ->leftJoin('nilai', 'siswa.nisn', '=', 'nilai.nisn')
            ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->distinct()
            ->pluck('siswa.id_kelas')
            ->toArray();
        Log::info('kelasList', ['kelasList' => $kelasList]);

        // untuk buat column sesuai kegiatan
        $kegiatanList = DB::table('nilai')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->toArray();
        Log::info('kegiatanList', ['kegiatanList' => $kegiatanList]);

        // query untuk minta data nilai dan lainnya
        $query = DB::table('nilai')
            ->leftJoin('siswa', 'nilai.nisn', '=', 'siswa.nisn')
            ->leftJoin('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
            ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->select(
                'siswa.nisn',
                'siswa.id_kelas',
                'siswa.nama_siswa',
                'mapel.nama_mapel',
                'mapel.id_mapel',
                'nilai.tahun_pelajaran',
                'nilai.semester',
                'guru_mapel.nip_guru_mapel',
                'guru_mapel.nama_guru'
            );

        // menerapkan setiap filter
        if ($request->has('mapel') && !empty($request->input('mapel'))) {
            $query->where('mapel.nama_mapel', $request->input('mapel'));
            Log::info('Applied mapel filter', ['mapel' => $request->input('mapel')]);
        }
        if ($request->has('tahun_pelajaran') && !empty($request->input('tahun_pelajaran'))) {
            $query->where('nilai.tahun_pelajaran', $request->input('tahun_pelajaran'));
            Log::info('Applied tahun_pelajaran filter', ['tahun_pelajaran' => $request->input('tahun_pelajaran')]);
        }
        if ($request->has('id_kelas') && !empty($request->input('id_kelas'))) {
            $query->where('siswa.id_kelas', $request->input('id_kelas'));
            Log::info('Applied id_kelas filter', ['id_kelas' => $request->input('id_kelas')]);
        }
        if ($request->has('semester') && !empty($request->input('semester'))) {
            $query->where('nilai.semester', $request->input('semester'));
            Log::info('Applied semester filter', ['semester' => $request->input('semester')]);
        }

        // Debug query
        $testData = $query->get();
        Log::info('Test query data', ['testData' => $testData->toArray()]);

        // simpan nilai nama kegiatan ke list
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
                'mapel.id_mapel',
                'nilai.tahun_pelajaran',
                'nilai.semester',
                'guru_mapel.nip_guru_mapel',
                'guru_mapel.nama_guru'
            )
            ->get();

        Log::info('Pivoted data', ['data_nilai' => $data_nilai->toArray()]);

        if ($request->ajax()) {
            return response()->json([
                'data_nilai' => $data_nilai,
                'kegiatanList' => $kegiatanList,
                'semesterList' => $semesterList,
                'nama_mapel' => $data_nilai->isEmpty() ? '' : $data_nilai[0]->nama_mapel
            ]);
        }

        return view('dashboard-guru-mapel', [
            'data_nilai' => $data_nilai,
            'kegiatanList' => $kegiatanList,
            'mapelList' => $mapelList,
            'tahunPelajaranList' => $tahunPelajaranList,
            'semesterList' => $semesterList,
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaran
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
                'value' => 'nullable|string',
                'tahun_pelajaran' => 'required|string',
                'semester' => 'required|in:Ganjil,Genap',
                'id_mapel' => 'required|string',
                'nip_guru_mapel' => 'required|string',
            ]);

            DB::beginTransaction();

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

            if ($record) {
                // Update existing record
                $updated = DB::table('nilai')
                    ->where('nisn', $nisn)
                    ->where('kegiatan', $field)
                    ->where('tahun_pelajaran', $tahun_pelajaran)
                    ->where('semester', $semester)
                    ->where('id_mapel', $id_mapel)
                    ->where('nip_guru_mapel', $nip_guru_mapel)
                    ->update(['nilai' => $value]);

                Log::info('Update operation', [
                    'updated_rows' => $updated,
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

            Log::info('storeKegiatan called', [
                'nip' => $nip,
                'request_data' => $request->all(),
            ]);

            $validated = $request->validate([
                'mapelSelect' => 'required|exists:mapel,id_mapel',
                'tahunSelect' => 'required|string',
                'inputKegiatan' => 'required|string|max:255',
            ]);

            $assigned = $this->verifyTeacherAccessToMapel($nip, $validated['mapelSelect']);

            if (!$assigned) {
                Log::error('Unauthorized access to mapel', [
                    'nip' => $nip,
                    'id_mapel' => $validated['id_mapel'],
                ]);
                return response()->json(['message' => 'Anda tidak memiliki akses ke mapel ini'], 403);
            }

            // Check if kegiatan already exists for this mapel and tahun_pelajaran
            $exists = DB::table('nilai')
                ->where('id_mapel', $validated['mapelSelect'])
                ->where('tahun_pelajaran', $validated['tahunSelect'])
                ->where('kegiatan', $validated['inputKegiatan'])
                ->exists();

            if ($exists) {
                Log::warning('Kegiatan already exists', [
                    'id_mapel' => $validated['mapelSelect'],
                    'tahun_pelajaran' => $validated['tahunSelect'],
                    'kegiatan' => $validated['inputKegiatan'],
                ]);
                return response()->json(['message' => 'Kegiatan sudah ada untuk mata pelajaran dan tahun pelajaran ini'], 409);
            }

            $students = DB::table('siswa')
                ->join('nilai', 'siswa.nisn', '=', 'nilai.nisn')
                ->where('nilai.id_mapel', $validated['mapelSelect'])
                ->where('nilai.tahun_pelajaran', $validated['tahunSelect'])
                ->where('nilai.nip_guru_mapel', $nip)
                ->distinct()
                ->pluck('siswa.nisn');

            DB::beginTransaction();
            foreach ($students as $nisn) {
                DB::table('nilai')->insert([
                    'nisn' => $nisn,
                    'id_mapel' => $validated['mapelSelect'],
                    'nip_guru_mapel' => $nip,
                    'tahun_pelajaran' => $validated['tahunSelect'],
                    'kegiatan' => $validated['inputKegiatan'],
                    'nilai' => null,
                ]);
            }
            DB::commit();

            Log::info('Kegiatan inserted successfully', [
                'id_mapel' => $validated['mapelSelect'],
                'tahun_pelajaran' => $validated['tahunSelect'],
                'kegiatan' => $validated['inputKegiatan'],
            ]);

            return response()->json(['message' => 'Kegiatan berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in storeKegiatan', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Data tidak valid', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeKegiatan', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menambahkan kegiatan'], 500);
        }
    }

    public function getListMapelByNipGuruMapel($nip_guru_mapel)
    {
        $listMapel = DB::table('guru_mapel')
            ->join('paket_mapel', 'guru_mapel.kode_paket', '=', 'paket_mapel.kode_paket')
            ->join('mapel', 'paket_mapel.id_mapel', '=', 'mapel.id_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip_guru_mapel)
            ->select('mapel.id_mapel', 'mapel.nama_mapel', 'guru_mapel.nip_guru_mapel')
            ->distinct() // agar tidak terjadi duplicate data
            ->pluck('mapel.nama_mapel')
            ->toArray();

        return $listMapel;
    }

    public function getTahunAjaranAktif()
    {
        $tahunAjaran = DB::table('tahun_ajaran')->where('is_current', 1)->value('tahun');

        return $tahunAjaran;
    }

    public function verifyTeacherAccessToMapel($nip, $mapel)
    {
        $assigned = DB::table('guru_mapel')
                ->join('paket_mapel', 'guru_mapel.kode_paket', '=', 'paket_mapel.kode_paket')
                ->where('guru_mapel.nip_guru_mapel', $nip)
                ->where('paket_mapel.id_mapel', $mapel)
                ->exists();

        return $assigned;
    }

}
