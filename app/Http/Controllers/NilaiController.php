<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    // public function fetchNilai()
    // {
    //     $data = DB::table('nilai')->get();
    //     return view('dashboard-guru-mapel', [
    //         'data' => $data
    //     ]);
    // }

    public function fetchNilai(Request $request)
    {
        // 197806152005011001
        // budi123
        $nip = session('userID');

        Log::info('NIP', ['nip' => $nip]);

        // Get mapel options
        $mapelList = DB::table('mapel')
            ->join('nilai', 'mapel.id_mapel', '=', 'nilai.id_mapel')
            ->join('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->distinct()
            ->pluck('mapel.nama_mapel')
            ->toArray();
        Log::info('mapelList', ['mapelList' => $mapelList]);

        // Get kegiatan list
        // simpan kegiatan (Quiz 1,2, etc) ke list
        // untuk menentukan header di UI dashboard guru mapel
        $kegiatanList = DB::table('nilai')
            ->distinct()
            ->orderBy('kegiatan')
            ->pluck('kegiatan')
            ->toArray();
        Log::info('kegiatanList', ['kegiatanList' => $kegiatanList]);

        // Test raw query
        $testData = DB::table('nilai')
            ->join('siswa', 'nilai.nisn', '=', 'siswa.nisn')
            ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->select('nilai.nisn', 'nilai.kegiatan', 'nilai.nilai', 'mapel.nama_mapel')
            ->get();
        Log::info('Test data', ['testData' => $testData->toArray()]);

        // Build main query
        $query = DB::table('nilai')
            ->join('siswa', 'nilai.nisn', '=', 'siswa.nisn')
            ->join('mapel', 'nilai.id_mapel', '=', 'mapel.id_mapel')
            ->join('guru_mapel', 'nilai.nip_guru_mapel', '=', 'guru_mapel.nip_guru_mapel')
            ->where('guru_mapel.nip_guru_mapel', $nip)
            ->select(
                'siswa.nisn',
                'siswa.id_kelas',
                'siswa.nama_siswa',
                'mapel.nama_mapel',
                'nilai.tahun_pelajaran',
                'guru_mapel.nama_guru'
            );

        // Apply mapel filter
        if ($request->has('mapel') && !empty($request->input('mapel'))) {
            $query->where('mapel.nama_mapel', $request->input('mapel'));
            Log::info('Applied mapel filter', ['mapel' => $request->input('mapel')]);
        }

        // Pivot nilai values
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
            'mapelList' => $mapelList
        ]);
    }

    public function inputNilai(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|integer',
            'id_mapel' => 'required|integer',
            'nilai' => 'required|array',
        ]);

        foreach ($validated['nilai'] as $kegiatan => $nilai) {
            DB::table('nilai')->insert([
                'nisn' => $validated['id_siswa'],
                'id_mapel' => $validated['id_mapel'],
                'nip_guru_mapel' => session('userID'),
                'kegiatan' => $kegiatan,
                'nilai' => $nilai,
            ]);
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function updateNilai(Request $request)
    {
        $nisn = trim((string) $request->input('nisn')); // Ensure string
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
}
