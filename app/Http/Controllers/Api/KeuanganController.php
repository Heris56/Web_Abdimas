<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Traits\LogActivity;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\StaffKeuangan;
use App\Models\TipePembayaran;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Log;


class KeuanganController extends Controller
{

    use LogActivity;

    public function getsiswa(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input("search");
        $status = $request->input("status");

        $query = Siswa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $siswa = $query->paginate($perPage);

        return response()->json([
            "message" => "Berhasil Fetch Siswa",
            "data" => $siswa->items(),
            "meta" => [
                "current_page" => $siswa->currentPage(),
                "last_page" => $siswa->lastPage(),
                "per_page" => $siswa->perPage(),
                "total" => $siswa->total(),
            ]
        ], 200);
    }
    public function getPembayaran(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input("search");
        $query = Tagihan::with('siswa');
        sleep(seconds: 0); // for debugging timeout

        $pembayaran = $query->paginate($perPage);
        return response()->json([
            "message" => "Berhasil Fetch data Pembayaran",
            "data" => $pembayaran->items(),
            "meta" => [
                "current_page" => $pembayaran->currentPage(),
                "last_page" => $pembayaran->lastPage(),
                "per_page" => $pembayaran->perPage(),
                "total" => $pembayaran->total(),
            ]
        ], 200);
    }
    public function getTipePembayaran(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input("search");

        $query = TipePembayaran::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tipe', "like", "%{$search}%");
            });
        }

        $tipePembayaran = $query->paginate($perPage);
        sleep(seconds: 0); // for debugging timeout

        return response()->json([
            "message" => "Berhasil Fetch data Tipe Pembayaran",
            "data" => $tipePembayaran->items(),
            "meta" => [
                "current_page" => $tipePembayaran->currentPage(),
                "last_page" => $tipePembayaran->lastPage(),
                "per_page" => $tipePembayaran->perPage(),
                "total" => $tipePembayaran->total(),
            ]
        ], 200);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'message' => "Berhasil Fetch Profile",
            'id' => $user->id,
            'nama' => $user->nama,
            'email' => $user->email,
        ], 200);
    }

    public function LoginKeuangan(Request $request)
    {
        $staff = StaffKeuangan::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json(['message' => 'email atau password salah']);
        }

        //generate token
        $token = $staff->createToken('staff-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $staff->id,
                'nama' => $staff->nama,
                'email' => $staff->email,
            ]
        ], 200);
    }

    public function LogoutKeuangan(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Logout berhasil"
        ], 200);
    }

    public function CreateACCKeuangan()
    {
        DB::beginTransaction();
        try {
            StaffKeuangan::create([
                'nama' => 'Darryl Rambi',
                'email' => 'darryl@example.com',
                'password' => Hash::make('rahasia123'),
                'status' => 'Aktif'
            ]);

            DB::commit();
            return response()->json(['message' => 'Akun staff keuangan berhasil dibuat'], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat akun staff keuangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ChangePassword(Request $request)
    {
        $request->validate([
            "new_password" => "required|min:8|confirmed"
        ]);

        $user = Auth::user();

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            "message" => "Password Berhasil Diganti"
        ], 200);

    }

    public function dataPembayaran(Request $request)
    {
        try {
            $dataPembayaran = Pembayaran::with('tagihan', 'tagihan.siswa', 'tagihan.tipePembayaran')->paginate();
            return response()->json([
                'message' => 'Berhasil Fetch Data Transaksi',
                'data' => $dataPembayaran
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Fetch Data Transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function dataPengeluaran(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input("search");
            $status = $request->input("status");

            $query = Pengeluaran::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                        ->orWhere('nominal', 'like', "%{$search}%");
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            $dataPengeluaran = $query->paginate($perPage);

            return response()->json([
                'message' => 'Berhasil Fetch Data Pengeluaran',
                'data' => $dataPengeluaran->items(),
                "meta" => [
                    "current_page" => $dataPengeluaran->currentPage(),
                    "last_page" => $dataPengeluaran->lastPage(),
                    "per_page" => $dataPengeluaran->perPage(),
                    "total" => $dataPengeluaran->total(),
                ],
                'links' => [
                    'next' => $dataPengeluaran->nextPageUrl(),
                    'prev' => $dataPengeluaran->previousPageUrl(),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Fetch Data Pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function insertPengeluaran(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ]);
        try {
            $pengeluaran = Pengeluaran::create([
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
                'tanggal' => $request->tanggal,
            ]);

            $this->logActivity(
                "create",
                "cashflow_pengeluaran",
                $pengeluaran->id_pengeluaran,
                null,
                $pengeluaran->getAttributes(),
                "tambah data pengeluaran baru"
            );

            return response()->json([
                'message' => 'Pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function insertPembayaran(Request $request)
    {
        $request->validate([
            'jumlah_pembayaran' => 'required|numeric',
            'id_pembayaran' => 'required|numeric|exists:cashflow_tagihan,id_pembayaran',
        ]);
        try {
            $pembayaran = Pembayaran::create([
                'jumlah_pembayaran' => $request->jumlah_pembayaran,
                'id_pembayaran' => $request->id_pembayaran,
            ]);

            $this->logActivity(
                "create",
                "cashflow_tagihan_pembayaran",
                $pembayaran->id_pembayaran,
                null,
                $pembayaran->getAttributes(),
                "tambah data pembayaran baru"
            );

            return response()->json([
                'message' => 'Pembayaran berhasil ditambahkan',
                'data' => $pembayaran
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function inputTagihanAllSiswa(Request $request)
    {
        $request->validate([
            'id_tipe_pembayaran' => 'required|exists:cashflow_tipe_pembayaran,id_tipe_pembayaran',
            'status_pembayaran' => 'nullable|string|in:belum_lunas,lunas,menunggu',
            'tanggal_pembuatan_tagihan' => 'nullable|date',
        ]);

        $status = $request->status_pembayaran ?? 'belum_lunas';
        $tanggalMulai = $request->tanggal_pembuatan_tagihan
            ? \Carbon\Carbon::parse($request->tanggal_pembuatan_tagihan)
            : now();

        // Ambil semua siswa
        $siswaList = Siswa::all();

        $tagihanData = [];
        foreach ($siswaList as $siswa) {
            for ($i = 0; $i < 12; $i++) {
                $jadwalPembayaran = $tanggalMulai->copy()->addMonths($i);

                $tagihanData[] = [
                    'nisn' => $siswa->nisn,
                    'id_tipe_pembayaran' => $request->id_tipe_pembayaran,
                    'status_pembayaran' => 'belum_lunas',
                    'tanggal_pembuatan_tagihan' => $tanggalMulai,
                    'jadwal_pembayaran' => $jadwalPembayaran->toDateString(),
                ];
            }
        }

        // Insert sekaligus (lebih cepat daripada create satu-satu)
        Tagihan::insert($tagihanData);

        $this->logActivity(
            "create",
            "cashflow_tagihan",
            null, // No specific ID for bulk insert
            null,
            ['total_records' => count($tagihanData)],
            "Menambah tagihan SPP untuk " . count($siswaList) . " siswa"
        );

        return response()->json([
            'message' => 'Tagihan berhasil dibuat untuk semua siswa selama 12 bulan',
            'total_siswa' => count($siswaList),
            'total_tagihan' => count($tagihanData),
        ], 201);
    }

    public function dataLog(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input("search");
            $status = $request->input("status");

            $query = ActivityLogs::query()->with('user');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('nama', 'like', "%{$search}%");
                        });
                });
            }


            if ($status) {
                $query->where('status', $status);
            }

            $log = $query->paginate($perPage);

            return response()->json([
                'message' => 'Berhasil Fetch Data Log',
                'data' => $log->items(),
                "meta" => [
                    "current_page" => $log->currentPage(),
                    "last_page" => $log->lastPage(),
                    "per_page" => $log->perPage(),
                    "total" => $log->total(),
                ],
                'links' => [
                    'next' => $log->nextPageUrl(),
                    'prev' => $log->previousPageUrl(),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Fetch Data Log',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
