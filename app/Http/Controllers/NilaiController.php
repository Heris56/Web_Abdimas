<?php

namespace App\Http\Controllers;

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
        $mapelList = DB::table('mapel')
            ->leftJoin('nilai', 'mapel.id_mapel', '=', 'nilai.id_mapel')
            ->leftJoin('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->distinct()
            ->pluck('mapel.nama_mapel')
            ->toArray();
        Log::info('mapelList', ['mapelList' => $mapelList]);

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
                'nilai.tahun_pelajaran',
                'nilai.semester',
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
                'nilai.tahun_pelajaran',
                'nilai.semester',
                'guru_mapel.nama_guru'
            )
            ->get();

        Log::info('Pivoted data', ['data_nilai' => $data_nilai->toArray()]);

        if ($request->ajax()) {
            return response()->json([
                'data_nilai' => $data_nilai,
                'kegiatanList' => $kegiatanList,
                'nama_mapel' => $data_nilai->isEmpty() ? '' : $data_nilai[0]->nama_mapel
            ]);
        }

        return view('dashboard-guru-mapel', [
            'data_nilai' => $data_nilai,
            'kegiatanList' => $kegiatanList,
            'mapelList' => $mapelList,
            'tahunPelajaranList' => $tahunPelajaranList,
            'kelasList' => $kelasList
        ]);
    }

    public function inputNilai(Request $request)
    {
        try {
            $nip = session('userID');
            Log::info('inputNilai called', [
                'nip' => $nip,
                'request' => $request->only(['nisn', 'kegiatan', 'nilai', 'mapel', 'tahun_pelajaran', 'id_kelas']),
            ]);

            // Validate input
            $validated = $request->validate([
                'nisn' => 'required|exists:siswa,nisn',
                'kegiatan' => 'required|string',
                'nilai' => 'required|array',
                'nilai.*' => 'nullable|numeric|min:0|max:100', // Allow array of grades
                'mapel' => 'required|exists:mapel,nama_mapel',
                'tahun_pelajaran' => 'required|string',
                'id_kelas' => 'nullable|string|exists:siswa,id_kelas',
            ]);

            // Get id_mapel from mapel name
            $id_mapel = $this->getMapelId($validated['mapel']);
            if (!$id_mapel) {
                Log::error('Mapel not found', ['mapel' => $validated['mapel']]);
                return response()->json(['message' => 'Mapel tidak ditemukan'], 404);
            }

            // Verify teacher has access to this mapel
            if (!$this->verifikasiGuruMapel($nip, $id_mapel)) {
                Log::error('Unauthorized access to mapel', ['nip' => $nip, 'id_mapel' => $id_mapel]);
                return response()->json(['message' => 'Anda tidak memiliki akses ke mapel ini'], 403);
            }

            // Update or insert grades
            DB::beginTransaction();
            $this->cekNilai($validated, $nip, $id_mapel);
            DB::commit();

            Log::info('Grades upserted successfully', [
                'nisn' => $validated['nisn'],
                'id_mapel' => $id_mapel,
                'tahun_pelajaran' => $validated['tahun_pelajaran'],
            ]);

            return response()->json(['message' => 'Nilai berhasil disimpan'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in inputNilai', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Data tidak valid', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in inputNilai', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan nilai'], 500);
        }
    }

    public function updateNilai(Request $request)
    {
        $nisn = trim((string) $request->input('nisn')); // pastikan keluaran string
        $field = trim($request->input('field'));
        $value = $request->input('value');

        Log::info('updateNilai inputs', [
            'nisn' => $nisn,
            'field' => $field,
            'value' => $value,
            'nisn_type' => gettype($nisn),
            'nisn_length' => strlen($nisn),
            'field_type' => gettype($field)
        ]);

        try {
            $record = DB::table('nilai')
                ->where('nisn', $nisn)
                ->where('kegiatan', $field)
                ->first();

            Log::info('Record query result', [
                'record' => $record,
                'query' => "SELECT * FROM nilai WHERE nisn = '$nisn' AND kegiatan = '$field'"
            ]);

            if ($record) {
                DB::table('nilai')
                    ->where('nisn', $nisn)
                    ->where('kegiatan', $field)
                    ->update(['nilai' => $value]);
                return response()->json(['success' => true, 'message' => 'Berhasil update nilai!']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found for nisn: ' . $nisn . ' and kegiatan: ' . $field
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error in updateNilai', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMapelId(string $mapelName): string
    {
        return DB::table('mapel')
            ->where('nama_mapel', $mapelName)
            ->value('id_mapel');
    }

    public function verifikasiGuruMapel(string $nip, int $id_mapel): bool
    {
        return DB::table('guru_mapel')
            ->where('nip_guru_mapel', $nip)
            ->where('id_mapel', $id_mapel)
            ->exists();
    }

    public function cekNilai(array $validated, string $nip, int $id_mapel): void
    {
        foreach ($validated['nilai'] as $kegiatan => $nilai) {
            if (!is_null($nilai)) {
                DB::table('nilai')->updateOrInsert(
                    [
                        'nisn' => $validated['nisn'],
                        'id_mapel' => $id_mapel,
                        'nip_guru_mapel' => $nip,
                        'tahun_pelajaran' => $validated['tahun_pelajaran'],
                        'kegiatan' => $kegiatan,
                        'semester' => $validated['semester'] ?? '1', // Default to semester 1
                        'tanggal' => $validated['tanggal'] ?? now()->toDateString(), // Default to today
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );

                Log::info('Grade upserted', [
                    'nisn' => $validated['nisn'],
                    'id_mapel' => $id_mapel,
                    'tahun_pelajaran' => $validated['tahun_pelajaran'],
                    'kegiatan' => $kegiatan,
                    'nilai' => $nilai,
                    'semester' => $validated['semester'] ?? '1',
                    'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
                ]);
            }
        }
    }
}
