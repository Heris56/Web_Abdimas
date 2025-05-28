<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function fetchData($type = 'siswa')
    {
        // supaya label buttons bisa dinamis
        $labels = [
            'siswa' => 'Siswa',
            'kelas' => 'Kelas',
            'guru_mapel' => 'Guru Mapel',
            'wali_kelas' => 'Wali Kelas',
            'mapel' => 'Mapel'
        ];

        $dropdowns = [
            'kelas' => DB::table('kelas')->get(['id_kelas', 'jurusan']),
            'mapel' => DB::table('mapel')->get(['id_mapel', 'nama_mapel']),
            'siswa' => DB::table('siswa')->get(['nisn', 'nama_siswa']),
            'guru_mapel' => DB::table('guru_mapel')->get(['nip_guru_mapel', 'nama_guru']),
            'wali_kelas' => DB::table('wali_kelas')->get(['nip_wali_kelas', 'nama']),
        ];

        switch ($type) {
            case 'siswa':
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'status_tahun_ajaran' => 'Status Tahun Ajaran',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
                break;
            case 'kelas':
                $data = DB::table('kelas')->get();
                $columns = [
                    'id_kelas' => 'Kelas',
                    'jurusan' => 'Jurusan'
                ];
                break;
            case 'guru_mapel':
                $data = DB::table('guru_mapel')->get();
                $columns = [
                    'nip_guru_mapel' => 'NIP',
                    'nama_guru' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status_tahun_ajaran' => 'Status Tahun Ajaran',
                    'id_mapel' => 'ID Mapel',
                    'id_kelas' => 'Kelas'
                ];
                break;
            case 'wali_kelas':
                $data = DB::table('wali_kelas')->get();
                $columns = [
                    'nip_wali_kelas' => 'NIP',
                    'nama' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status_tahun_ajaran' => 'Status Tahun Ajaran',
                    'id_mapel' => 'ID Mapel',
                    'id_kelas' => 'Kelas'
                ];
                break;
            case 'mapel':
                $data = DB::table('mapel')->get();
                $columns = [
                    'id_mapel' => 'ID Mapel',
                    'nama_mapel' => 'Nama Mapel'
                ];
                break;
            default:
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'status_tahun_ajaran' => 'Status Tahun Ajaran',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
                $type = 'siswa'; // set default untuk selalu show data siswa
        }

        // agar di view tidak ada '_' atau spasi kosong ' '
        $buttonText = $labels[$type] ?? str_replace('_', ' ', ucwords($type));

        return view('dashboard-staff', [
            'data' => $data,
            'columns' => $columns,
            'type' => $type,
            'dropdowns' => $dropdowns,
            'buttonText' => $buttonText
        ]);
    }

    public function inputData(Request $request, $type)
    {
        Log::info("storeData called for type: {$type}, Input: " . json_encode($request->all()));
        $rules = [
            'siswa' => [
                'nisn' => 'required|unique:siswa,nisn',
                'nama_siswa' => 'required|string|max:255',
                'id_kelas' => 'required|exists:kelas,id_kelas',
                'status' => 'required|in:aktif,nonaktif',
                'status_tahun_ajaran' => 'required|in:aktif,nonaktif',
                'tahun_ajaran' => 'required|string|max:10',
            ],
            'kelas' => [
                'id_kelas' => 'required|unique:kelas,id_kelas',
                'jurusan' => 'required|string|max:32',
            ],
            'guru_mapel' => [
                'nip_guru_mapel' => 'required|unique:guru_mapel,nip_guru_mapel',
                'nama_guru' => 'required|string|max:255',
                'tahun_ajaran' => 'required|string|max:10',
                'status_tahun_ajaran' => 'required|in:aktif,nonaktif',
                'id_mapel' => 'required|exists:mapel,id_mapel',
                'id_kelas' => 'required|exists:kelas,id_kelas',
            ],
            'wali_kelas' => [
                'nip_wali_kelas' => 'required|unique:wali_kelas,nip_wali_kelas',
                'nama' => 'required|string|max:255',
                'tahun_ajaran' => 'required|string|max:10',
                'status_tahun_ajaran' => 'required|in:aktif,nonaktif',
                'id_kelas' => 'required|exists:kelas,id_kelas',
            ],
            'mapel' => [
                'id_mapel' => 'required|unique:mapel,id_mapel',
                'nama_mapel' => 'required|string|max:255',
            ],
        ];

        $validated = $request->validate($rules[$type] ?? []);

        try {
            Log::info("Checking if rules exist for type: {$type}");
            if (!isset($rules[$type])) {
                Log::error("No validation rules defined for type: {$type}");
                return response()->json(['success' => false, 'message' => "Invalid type: {$type}"], 400);
            }

            Log::info("Starting validation for type: {$type}");
            $validated = $request->validate($rules[$type]);
            Log::info("Validated data for type {$type}: " . json_encode($validated));

            Log::info("Processing insertion for type: {$type}");
            switch ($type) {
                case 'siswa':
                    $validated['password'] = md5($validated['nisn']);
                    $result = DB::table('siswa')->insert($validated);
                    Log::info("Siswa insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                case 'guru_mapel':
                    $validated['password'] = md5($validated['nip_guru_mapel']);
                    $result = DB::table('guru_mapel')->insert($validated);
                    Log::info("Guru_mapel insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                case 'wali_kelas':
                    $validated['password'] = md5($validated['nip_wali_kelas']);
                    $result = DB::table('wali_kelas')->insert($validated);
                    Log::info("Wali_kelas insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                case 'kelas':
                    $result = DB::table('kelas')->insert($validated);
                    Log::info("Kelas insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                case 'mapel':
                    $result = DB::table('mapel')->insert($validated);
                    Log::info("Mapel insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                default:
                    Log::error("Unexpected type in switch: {$type}");
                    return response()->json(['success' => false, 'message' => "Invalid type: {$type}"], 400);
            }

            if (!$result) {
                Log::error("Insert failed for type: {$type}");
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan data ke database!'], 500);
            }

            Log::info("Data successfully saved for type: {$type}");
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error for type {$type}: " . json_encode($e->errors()));
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Exception in storeData for type {$type}: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}
