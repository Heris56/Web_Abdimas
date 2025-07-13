<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // Import ValidationException untuk penanganan error
use Illuminate\Support\Facades\Session; // Use Session facade for clarity
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DataController extends Controller
{
    private $staffid;
    public function __construct()
    {
        // The __construct method is executed when the controller is instantiated.
        // At this point, session and cookie helpers are available.

        // Check session first, then cookie, then default to null
        $this->staffid = Session::get('userID');

        if (is_null($this->staffid)) {
            $this->staffid = Cookie::get('userID');
        }

        // If still null, it remains null
        // You could also add:
        // if (is_null($this->staffid)) {
        //     // Optionally, redirect to login if ID is absolutely required for any action
        //     // return redirect()->route('login'); // This would need to be handled differently
        // }
    }

    public $rules = [
        'siswa' => [
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'status' => 'required|in:aktif,nonaktif',
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
            'status' => 'required|in:aktif,nonaktif',
            'kode_paket' => 'exists:paket_mapel,kode_paket',
            // 'id_mapel' => 'required|exists:mapel,id_mapel',
            // 'id_kelas' => 'required|exists:kelas,id_kelas',
        ],
        'wali_kelas' => [
            'nip_wali_kelas' => 'required|unique:wali_kelas,nip_wali_kelas',
            'nama' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:10',
            'status' => 'required|in:aktif,nonaktif',
            'id_kelas' => 'required|exists:kelas,id_kelas',
        ],
        'mapel' => [
            'id_mapel' => 'required|unique:mapel,id_mapel',
            'nama_mapel' => 'required|string|max:255',
        ],
        'tahun_ajaran' => [
            'tahun' => ['required', 'string', 'max:10'],
            'semester' => 'required|string|max:10|in:Genap,Ganjil'
        ],
        'paket_mapel' => [
            'kode_paket' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => [
                'required',
                'exists:mapel,id_mapel',
                // // Ini akan memastikan kombinasi kode_paket, id_kelas, id_mapel unik
                // Rule::unique('paket_mapel')->where(function ($query) use ($request) {
                //         return $query->where('kode_paket', $request->kode_paket)
                //             ->where('id_kelas', $request->id_kelas);
                //     }),
            ],
            'tahun_ajaran' => 'required|string|max:10',
        ],
    ];

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
            'paket_mapel' => DB::table('paket_mapel')->select('kode_paket')->distinct()->get(),
        ];

        switch ($type) {
            case 'siswa':
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
                break;
            case 'kelas':
                $data = DB::table('kelas')->get();
                $columns = [
                    'id_kelas' => 'Kelas',
                    'jurusan' => 'Jurusan',
                ];
                break;
            case 'guru_mapel':
                $data = DB::table('guru_mapel')->get();
                $columns = [
                    'nip_guru_mapel' => 'Kode Guru',
                    'nama_guru' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status' => 'Status',
                    'kode_paket' => 'Kode Paket'
                    // 'id_mapel' => 'ID Mapel',
                    // 'id_kelas' => 'Kelas'
                ];
                break;
            case 'wali_kelas':
                $data = DB::table('wali_kelas')->get();
                $columns = [
                    'nip_wali_kelas' => 'Kode Guru',
                    'nama' => 'Nama Guru',
                    'tahun_ajaran' => 'Tahun Ajaran',
                    'status' => 'Status',
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
            case 'tahun_ajaran':
                $data = DB::table('tahun_ajaran')
                    ->orderBy('tahun', 'asc')
                    ->orderBy('semester', 'asc')->get();
                $columns = [
                    'tahun' => 'Tahun Ajaran',
                    'semester' => 'Semester',
                    'is_current' => 'Tahun berjalan'
                ];
                break;
            case 'paket_mapel':
                $data = DB::table('paket_mapel')->get();
                $columns = [
                    'kode_paket' => 'Kode Paket',
                    'id_kelas' => 'Kelas',
                    'id_mapel' => 'Mapel',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];


                break;
            default:
                $data = DB::table('siswa')->get();
                $columns = [
                    'nisn' => 'NISN',
                    'nama_siswa' => 'Nama Siswa',
                    'id_kelas' => 'Kelas',
                    'status' => 'Status',
                    'tahun_ajaran' => 'Tahun Ajaran'
                ];
                $type = 'siswa'; // set default untuk selalu show data siswa
        }

        // agar di view tidak ada '_' atau spasi kosong ' '
        $buttonText = $labels[$type] ?? str_replace('_', ' ', ucwords($type));

        $currentyear = DB::table('tahun_ajaran')->where('is_current', 1)->first();
        return view('dashboard-staff', [
            'data' => $data,
            'columns' => $columns,
            'type' => $type,
            'dropdowns' => $dropdowns,
            'buttonText' => $buttonText,
            'current_year' => $currentyear
        ]);
    }

    public function inputData(Request $request, $type)
    {
        Log::info("storeData called for type: {$type}, Input: " . json_encode($request->all()));

        $validated = $request->validate($this->rules[$type] ?? []);

        try {
            Log::info("Checking if rules exist for type: {$type}");
            if (!isset($this->rules[$type])) {
                Log::error("No validation rules defined for type: {$type}");
                return response()->json(['success' => false, 'message' => "Invalid type: {$type}"], 400);
            }

            $validationRules = $this->rules[$type]; // Ambil aturan dasar

            if ($type === 'paket_mapel') {
                $validationRules['id_mapel'][] = Rule::unique('paket_mapel')->where(function ($query) use ($request) {
                    return $query->where('kode_paket', $request->kode_paket)
                        ->where('id_kelas', $request->id_kelas);
                });
            }
            if ($type === 'tahun_ajaran') {
                $validationRules['tahun'][] = Rule::unique('tahun_ajaran')->where(function ($query) use ($request) {
                    return $query->where('tahun', $request->tahun)
                        ->where('semester', $request->semester);
                });
            }

            // --- DEFINISIKAN PESAN KUSTOM DI SINI ---
            $messages = [];
            if ($type === 'tahun_ajaran') {
                // Kunci pesan adalah 'nama_field.nama_aturan'
                $messages['tahun.unique'] = 'Tahun ajaran dan semester ini sudah ada. Tidak boleh duplikasi.';
            }

            Log::info("Starting validation for type: {$type}");
            $validated = $request->validate($validationRules, $messages);
            Log::info(message: "Validated data for type {$type}: " . json_encode($validated));

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
                case 'tahun_ajaran':
                    $result = DB::table('tahun_ajaran')->insert($validated);
                    Log::info("Tahun ajaran insert result: " . ($result ? 'Success' : 'Failed'));
                    break;
                case 'paket_mapel':
                    $result = DB::table('paket_mapel')->insert($validated);
                    Log::info("Paket mapel insert result: " . ($result ? 'Success' : 'Failed'));
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
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (ValidationException $e) {
            Log::error("Validation error for type {$type}: " . json_encode($e->errors()));
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Exception in storeData for type {$type}: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data!');
        }
    }

    public function Updatedata(Request $request, $type, $id)
    {
        $id = trim($id);
        Log::info("updateData called for type: {$type}, ID: {$id}, Input: " . json_encode($request->all()));
        try {
            if (!isset($this->rules[$type])) {
                Log::error("No validation rules defined for type: {$type}");
                return response()->json(['success' => false, 'message' => "Invalid type: {$type}"], 400);
            }

            $validationRules = $this->rules[$type];
            $primaryKey = ''; // Inisialisasi

            switch ($type) {
                case 'siswa':
                    $primaryKey = 'nisn';
                    $validationRules['nisn'] = 'required|string|max:20|unique:siswa,nisn,' . $id . ',' . $primaryKey;
                    break;
                case 'kelas':
                    $primaryKey = 'id_kelas';
                    $validationRules['id_kelas'] = 'required|string|max:10|unique:kelas,id_kelas,' . $id . ',' . $primaryKey;
                    break;
                case 'guru_mapel':
                    $primaryKey = 'nip_guru_mapel';
                    $validationRules['nip_guru_mapel'] = 'required|string|max:20|unique:guru_mapel,nip_guru_mapel,' . $id . ',' . $primaryKey;
                    break;
                case 'wali_kelas':
                    $primaryKey = 'nip_wali_kelas';
                    $validationRules['nip_wali_kelas'] = 'required|string|max:20|unique:wali_kelas,nip_wali_kelas,' . $id . ',' . $primaryKey;
                    break;
                case 'mapel':
                    $primaryKey = 'id_mapel';
                    $validationRules['id_mapel'] = 'required|string|max:10|unique:mapel,id_mapel,' . $id . ',' . $primaryKey;
                    break;
                case 'tahun_ajaran':
                    $primaryKey = 'id_tahun_ajaran';
                    $validationRules['tahun'][] = Rule::unique('tahun_ajaran')->where(function ($query) use ($request) {
                        return $query->where('tahun', $request->tahun)
                            ->where('semester', $request->semester);
                    })->ignore($id, $primaryKey); // <--- PENTING: Mengecualikan record saat ini
                    break;
                case 'paket_mapel':
                    $primaryKey = 'id_paket';
                    $validationRules['id_mapel'][] = Rule::unique('paket_mapel')->where(function ($query) use ($request) {
                        return $query->where('kode_paket', $request->kode_paket)
                            ->where('id_kelas', $request->id_kelas);
                    })->ignore($id, $primaryKey);
                    break;
                default:
                    Log::error("Unexpected type for update in switch: {$type}");
                    return redirect()->back()->with('error', "Tipe data tidak valid untuk update: {$type}");
            }

            Log::info("DEBUG - Resolved Primary Key: {$primaryKey}");
            Log::info("DEBUG - Value of Primary Key from Form (\$validatedData[\$primaryKey]): " . ($request->input($primaryKey) ?? 'N/A'));
            Log::info("DEBUG - Value from URL (\$id): {$id}");

            $messages = [];
            if ($type === 'tahun_ajaran') {
                // Kunci pesan adalah 'nama_field.nama_aturan'
                $messages['tahun.unique'] = 'Tahun ajaran dan semester ini sudah ada. Tidak boleh duplikasi.';
            }

            // Lakukan validasi dengan aturan yang sudah dimodifikasi
            $validatedData = $request->validate($validationRules, $messages);

            $table = $type;

            // Password akan di-generate ulang dari primary key yang di-submit via form
            if (in_array($type, ['siswa', 'guru_mapel', 'wali_kelas'])) {
                // Gunakan nilai primary key dari $validatedData (mungkin nilai baru jika diubah)
                // Jika primary key memang bagian dari input form yang di-validate
                $ischanged = DB::table($type)->where($primaryKey, $id)->value('pwd_is_changed');
                if ($ischanged == false) {
                    $validatedData['password'] = Hash::make($validatedData[$primaryKey]);
                }
            }

            // Lakukan update data
            // Gunakan $primaryKey dan $id untuk menargetkan record yang benar
            $updateResult = DB::table($table)->where($primaryKey, $id)->update($validatedData);

            // update() method mengembalikan jumlah baris yang terpengaruh.
            // Jika 0, bisa jadi tidak ada perubahan yang terdeteksi atau ID tidak ditemukan.
            if ($updateResult === 0) {
                Log::warning("No changes detected or update failed for type {$type} ID {$id}. Data: " . json_encode($validatedData));
                return redirect()->back()->with('info', 'Tidak ada perubahan yang disimpan atau data sudah terbaru.');
            }

            Log::info("Data successfully updated for type: {$type}, ID: {$id}");
            return redirect()->back()->with('success', 'Data berhasil diperbarui!');
        } catch (ValidationException $e) {
            Log::error("Validation error for update type {$type}: " . json_encode($e->errors()));
            // Laravel akan otomatis mengisi errors ke session dan mengembalikan input
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Exception in updateData for type {$type}: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function ConfirmPassword(Request $request)
    {
        try {
            $id = $this->staffid;
            $userpassword = DB::table('staff')->where('nip_staff', $id)->value('password');

            $request->validate([
                'password_admin' => 'required|max:20'
            ]);
            $idTahunAjaran = $request->input('id_tahun_ajaran');

            $inputpassword = $request->input('password_admin');

            $ischanged = DB::table('tahun_ajaran')->where('id_tahun_ajaran', $idTahunAjaran)->value('is_changed');

            if (Hash::check($inputpassword, $userpassword)) {
                if ($ischanged == false) {
                    $tahunajaran = DB::table('tahun_ajaran')->where('id_tahun_ajaran', $idTahunAjaran)->value('tahun');
                    // nonaktifkan active marker terlebih dahulu
                    DB::table('tahun_ajaran')->where('active_year_marker', 1)->update(['is_current' => 0]);

                    // set mark jadi active lagi di tahun yang dipilih
                    DB::table('tahun_ajaran')->where('id_tahun_ajaran', $idTahunAjaran)->update(['is_current' => 1]);

                    // update semua tahun ajaran siswa
                    DB::table('siswa')->where('status', "aktif")->update(['tahun_ajaran' => $tahunajaran]);

                    // update tahun ajaran guru_mapel
                    DB::table('guru_mapel')->where('status', "aktif")->update(['tahun_ajaran' => $tahunajaran]);
                    // update tahun ajaran wali_kelas
                    DB::table('wali_kelas')->where('status', "aktif")->update(['tahun_ajaran' => $tahunajaran]);

                    DB::table('tahun_ajaran')->where('id_tahun_ajaran', $idTahunAjaran)->update(['is_changed' => true]);
                    return redirect()->back()->with('success', 'Konfirmasi password berhasil! Tahun ajaran berhasil berubah');
                } else {
                    return redirect()->back()->with('error', 'Gagal Mengganti Tahun ajaran, tahun ajaran sudah pernah diubah sebelumnya');
                }

            } else {
                return redirect()->back()->withErrors(['password_admin' => 'Password admin salah.'])->withInput()->with('show_confirm_password_modal', true);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat konfirmasi' . $e->getMessage());
        }
    }
}
