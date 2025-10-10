<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagihanRequest;
use App\Models\ActivityLogs;
use App\Models\Kas;
use App\Models\KasTransaksi;
use App\Models\TahunAjaran;
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
        try {
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
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Get Siswa',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function getPembayaran(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input("search");
            $query = Pembayaran::with('tagihan.siswa');
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
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Get Pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function getTipePembayaran(Request $request)
    {
        try {
            $perPage = $request->input('per_page', );
            $search = $request->input("search");

            $query = TipePembayaran::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_tipe', "like", "%{$search}%");
                });
            }

            if ($perPage && is_numeric($perPage) && $perPage > 0) {
                // Kalau ada per_page -> pakai paginate
                $tipePembayaran = $query->paginate($perPage);

                return response()->json([
                    "message" => "Berhasil Fetch data Tipe Pembayaran (paginate)",
                    "data" => $tipePembayaran->items(),
                    "meta" => [
                        "current_page" => $tipePembayaran->currentPage(),
                        "last_page" => $tipePembayaran->lastPage(),
                        "per_page" => $tipePembayaran->perPage(),
                        "total" => $tipePembayaran->total(),
                    ]
                ], 200);
            } else {
                // Kalau gak ada per_page -> ambil semua data
                $tipePembayaran = $query->get();

                return response()->json([
                    "message" => "Berhasil Fetch semua data Tipe Pembayaran",
                    "data" => $tipePembayaran
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Get Tipe Pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json([
                'message' => "Berhasil Fetch Profile",
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Get Pofile',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function getTahunAjaran(Request $request)
    {
        try {
            $tahunAjaran = TahunAjaran::get();
            $currentTahunAjaran = TahunAjaran::where("is_current", true)->first();
            return response()->json([
                'message' => "Berhasil Fetch Tahun Ajaran",
                "data" => $tahunAjaran,
                "currentTahunAjaran" => $currentTahunAjaran
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Get Pofile',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function LoginKeuangan(Request $request)
    {
        DB::beginTransaction();
        try {
            $staff = StaffKeuangan::where('email', $request->email)->first();

            if (!$staff || !Hash::check($request->password, $staff->password)) {
                return response()->json(['message' => 'email atau password salah']);
            }

            //generate token
            $token = $staff->createToken('staff-token')->plainTextToken;
            DB::commit();

            return response()->json([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id' => $staff->id,
                    'nama' => $staff->nama,
                    'email' => $staff->email,
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Login Keuangan',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function LogoutKeuangan(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->user()->currentAccessToken()->delete();
            DB::commit();

            return response()->json([
                "message" => "Logout berhasil"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function CreateACCKeuangan()
    {
        DB::beginTransaction();
        try {
            StaffKeuangan::create([
                'nama' => 'Raphael Permana Barus',
                'email' => 'raphael@example.com',
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
        DB::beginTransaction();
        try {
            $request->validate([
                "new_password" => "required|min:8|confirmed"
            ]);

            $user = Auth::user();

            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();

            return response()->json([
                "message" => "Password Berhasil Diganti"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Mengganti password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function TagihanSiswa(Request $request)
    {
        try {
            $request->validate([
                'nisn' => 'required',
                'tipe' => 'required',
                'idTahunAjaran' => 'nullable|integer',
            ]);

            $currentTahunAjaran = TahunAjaran::where("is_current", true)->first();
            $idTahunAjaran = $request->idTahunAjaran ?? $currentTahunAjaran->id_tahun_ajaran;
            $tahun_ajaran = TahunAjaran::find($idTahunAjaran);

            $tipePeriode = TipePembayaran::where("id_tipe_pembayaran", $request->tipe)->value("tipe_periodik");
            $query = Tagihan::with(['tipePembayaran', 'tahunAjaran'])->where("id_tipe_pembayaran", $request->tipe)
                ->where("nisn", $request->nisn);
            switch ($tipePeriode) {
                case "sekali":
                    break;
                case "tahunan":
                    $query->whereHas("tahunAjaran", function ($q) use ($tahun_ajaran) {
                        $q->where("tahun", $tahun_ajaran->tahun);
                    });
                    break;
                case "semester":
                    $query->where("id_tahun_ajaran", $idTahunAjaran);

                    break;
                case "bulanan":
                    $query->where("id_tahun_ajaran", $idTahunAjaran);

                    break;
                default:
                    return response()->json([
                        'message' => 'Tidak ditemukan tipe tagihan',
                    ], 404);
            }

            $data = $query->get();

            return response()->json([
                "message" => "Berhasil Fetch data Tagihan Siswa",
                "data" => $data,
                "currentTahunAjaran" => $currentTahunAjaran
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Mendapatkan Tagihan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createTagihan(TagihanRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $bulanSemester = [
                "Ganjil" => ["Januari", "Februari", "Maret", "April", "Mei", "Juni"],
                "Genap" => ["Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            ];


            $nisn = $data['nisn'];
            $tipeTagihanID = $data['id_tipe_pembayaran'];
            $idTahunAjaran = $data['id_tahun_ajaran'] ?? null;
            $tahunAjaran = TahunAjaran::find($idTahunAjaran);

            $tipePeriode = TipePembayaran::where("id_tipe_pembayaran", $tipeTagihanID)->value("tipe_periodik");
            $tipetagihan = TipePembayaran::where("id_tipe_pembayaran", $tipeTagihanID)->value("nama_tipe");

            switch ($tipePeriode) {
                case "sekali":
                    Tagihan::create([
                        "nisn" => $nisn,
                        "status_tagihan" => "Belum Lunas",
                        'id_tipe_pembayaran' => $tipeTagihanID,
                        'id_tahun_ajaran' => null,
                        'tanggal_pembuatan_tagihan' => now(),
                        'nominal_tagihan' => TipePembayaran::where('id_tipe_pembayaran', $tipeTagihanID)->value('nominal')
                    ]);
                    break;
                case "tahunan":
                    if ($idTahunAjaran && $idTahunAjaran != 0) {
                        Tagihan::create([
                            "nisn" => $nisn,
                            "status_tagihan" => "Belum Lunas",
                            'id_tipe_pembayaran' => $tipeTagihanID,
                            'id_tahun_ajaran' => $idTahunAjaran,
                            'periode' => $tahunAjaran["tahun"],
                            'tanggal_pembuatan_tagihan' => now(),
                            'nominal_tagihan' => TipePembayaran::where('id_tipe_pembayaran', $tipeTagihanID)->value('nominal')
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Gagal menambahkan Tagihan',
                        ], 400);
                    }

                    break;
                case "semester":
                    if ($idTahunAjaran && $idTahunAjaran != 0) {
                        Tagihan::create([
                            "nisn" => $nisn,
                            "status_tagihan" => "Belum Lunas",
                            'id_tipe_pembayaran' => $tipeTagihanID,
                            'id_tahun_ajaran' => $idTahunAjaran,
                            'periode' => $tahunAjaran["tahun"] . '-' . $tahunAjaran["semester"],
                            'tanggal_pembuatan_tagihan' => now(),
                            'nominal_tagihan' => TipePembayaran::where('id_tipe_pembayaran', $tipeTagihanID)->value('nominal')
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Gagal menambahkan Tagihan',
                        ], 400);
                    }

                    break;
                case "bulanan":
                    if ($idTahunAjaran && $idTahunAjaran != 0) {
                        $listBulan = $bulanSemester[$tahunAjaran["semester"]] ?? [];
                        $nominal = TipePembayaran::where('id_tipe_pembayaran', $tipeTagihanID)->value('nominal');
                        foreach ($listBulan as $bulan) {
                            Tagihan::create([
                                "nisn" => $nisn,
                                "status_tagihan" => "Belum Lunas",
                                'id_tipe_pembayaran' => $tipeTagihanID,
                                'id_tahun_ajaran' => $idTahunAjaran,
                                'periode' => $bulan,
                                'tanggal_pembuatan_tagihan' => now(),
                                'nominal_tagihan' => $nominal,
                            ]);
                        }

                    } else {
                        return response()->json([
                            'message' => 'Gagal menambahkan Tagihan',
                        ], 400);
                    }
                    break;
                default:
                    return response()->json([
                        'message' => 'Tidak ditemukan tipe tagihan',
                    ], 404);
            }

            DB::commit();

            return response()->json(["message" => "Berhasil menambahkan $tipetagihan"], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Menambahkan Tagihan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getKas()
    {
        try {
            $kas = Kas::get();

            return response()->json([
                "message" => "Berhasil Fetch data Kas",
                "data" => $kas,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal fetch Kas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createKasDefault(Request $request)
    {
        DB::beginTransaction();
        try {
            $listTipeKas = TipePembayaran::get();
            foreach ($listTipeKas as $tipekas) {
                $kas = Kas::where("id_tipe_pembayaran", $tipekas->id_tipe_pembayaran)->first();
                if (!$kas) {
                    Kas::create(
                        [
                            "id_tipe_pembayaran" => $tipekas->id_tipe_pembayaran,
                            "nama_kas" => $tipekas->nama_tipe,
                            "saldo" => 0,
                        ]
                    );
                }
            }
            DB::commit();

            return response()->json(["message" => "Berhasil menambahkan semua Kas"], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Menambahkan Kas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createPembayaran(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "jumlah_pembayaran" => "required|numeric|min:0",
                "id_tagihan" => "required|integer",
            ]);

            $listPembayaran = Pembayaran::where("id_tagihan", $request->id_tagihan)->get();
            $tagihan = Tagihan::findOrFail($request->id_tagihan);
            $totalTagihan = $tagihan->nominal_tagihan;

            $idKas = Kas::where("id_tipe_pembayaran", $tagihan->id_tipe_pembayaran)->value("id_kas");
            if (!$idKas) {
                return response()->json([
                    "message" => "Kas untuk tipe pembayaran ini tidak ditemukan",
                ], 404);
            }

            $kas = Kas::findOrFail($idKas);

            // define listnya kosong atau nggak
            if ($listPembayaran->isNotEmpty()) { // gak kosong
                $totalPembayaran = 0;

                // cari semua pembayaran terus akumulasiin total pembayaran (semua pembayaran dengan tagihan terkait)
                foreach ($listPembayaran as $pembayaran) {
                    $totalPembayaran = $totalPembayaran + $pembayaran->jumlah_pembayaran;
                }
                $totalPembayaran += $request->jumlah_pembayaran; // jumlah akhir pembayaran

                // tentukan jika total pembayaran melebihi total tagihan atau tidak
                if ($totalPembayaran <= $totalTagihan) {

                    // create pembayaran
                    $pembayaran = Pembayaran::create([
                        "jumlah_pembayaran" => $request->jumlah_pembayaran,
                        "id_tagihan" => $request->id_tagihan,
                    ]);

                    $saldoAkhir = $kas->saldo + $request->jumlah_pembayaran;

                    // create Transaksi Kas
                    KasTransaksi::create([
                        "id_kas" => $idKas,
                        "sumber" => "pembayaran",
                        "id_sumber" => $pembayaran->id_tagihan_pembayaran,
                        "tanggal" => now(),
                        "keterangan" => "Pembayaran tagihan #{$tagihan->id_tagihan}",
                        "debit" => $request->jumlah_pembayaran,
                        "kredit" => 0,
                        "saldo_akhir" => $saldoAkhir,
                    ]);

                    $kas->update([
                        "saldo" => $saldoAkhir
                    ]);

                } else {

                    // return, karna jumlah pembayaran melebihi tagihan
                    return response()->json([
                        "message" => "gagal menambahkan Pembayaran karena jumlah pembayaran melebihi batas tagihan yang harus dibayar",
                    ], 400);

                }

                if ($totalPembayaran >= $totalTagihan) {
                    // update tagihan jadi lunas kalo total pembayaran udah sama dengan tagihan
                    $tagihan->update([
                        "status_tagihan" => "Lunas"
                    ]);
                }
            } else { // kosong
                $totalPembayaran = $request->jumlah_pembayaran;

                // tentukan jika total pembayaran melebihi total tagihan atau tidak
                if ($totalPembayaran <= $totalTagihan) {
                    $pembayaran = Pembayaran::create([
                        "jumlah_pembayaran" => $request->jumlah_pembayaran,
                        "id_tagihan" => $request->id_tagihan,
                    ]);

                    $saldoAkhir = $kas->saldo + $request->jumlah_pembayaran;

                    // create Transaksi Kas
                    KasTransaksi::create([
                        "id_kas" => $idKas,
                        "sumber" => "pembayaran",
                        "id_sumber" => $pembayaran->id_tagihan_pembayaran,
                        "tanggal" => now(),
                        "keterangan" => "Pembayaran tagihan #{$tagihan->id_tagihan}",
                        "debit" => $request->jumlah_pembayaran,
                        "kredit" => 0,
                        "saldo_akhir" => $saldoAkhir,
                    ]);

                    $kas->update([
                        "saldo" => $saldoAkhir
                    ]);

                } else {
                    return response()->json([
                        "message" => "gagal menambahkan Pembayaran karena jumlah pembayaran melebihi batas tagihan yang harus dibayar",
                    ], 400);
                }

                if ($totalPembayaran >= $totalTagihan) {
                    // update tagihan jadi lunas kalo total pembayaran udah sama dengan tagihan
                    $tagihan->update([
                        "status_tagihan" => "Lunas"
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                "message" => "Berhasil menambahkan Pembayaran",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Menambahkan Pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTransaksiKas(Request $request)
    {
        try {
            $idKas = $request->input("idKas");
            if (!$idKas || $idKas == 0) {
                return response()->json([
                    "message" => "Gagal Fetch Kas Transaksi, id Kas Tidak ditemukan",
                    "data" => [],
                ]);
            }

            $perPage = $request->input('per_page', 10);
            $search = $request->input("search");

            $query = KasTransaksi::query()->where("id_kas", $idKas);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%");
                });
            }

            $kasTransaksi = $query->paginate($perPage);

            return response()->json([
                "message" => "Berhasil Fetch Kas Transaksi",
                "data" => $kasTransaksi->items(),
                "meta" => [
                    "current_page" => $kasTransaksi->currentPage(),
                    "last_page" => $kasTransaksi->lastPage(),
                    "per_page" => $kasTransaksi->perPage(),
                    "total" => $kasTransaksi->total(),
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal fetch Transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function dataPembayaran(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input("search");
            $dataPembayaran = Pembayaran::with('tagihan', 'tagihan.siswa', 'tagihan.tipePembayaran')->paginate();
            return response()->json([
                'message' => 'Berhasil Fetch Data Transaksi',
                'data' => $dataPembayaran,
                "meta" => [
                    "current_page" => $dataPembayaran->currentPage(),
                    "last_page" => $dataPembayaran->lastPage(),
                    "per_page" => $dataPembayaran->perPage(),
                    "total" => $dataPembayaran->total(),
                ],
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
            $tipe = $request->input("tipe", 0);

            $query = Pengeluaran::with(['tipe_kas']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                        ->orWhere('nominal', 'like', "%{$search}%");
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($tipe && $tipe != 0) {
                $query->where("id_kas", $tipe);
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
            'nominal' => 'required|numeric|min:0|max:10000000',
            'keterangan' => 'required|string|max:255',
            'id_kas' => 'required|integer',
        ]);
        DB::beginTransaction();
        try {
            $pengeluaran = Pengeluaran::create([
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
                'tanggal' => now(),
                'id_kas' => $request->id_kas
            ]);

            $kas = Kas::find($request->id_kas);
            $saldoAkhir = $kas->saldo - $request->nominal;
            // dd(KasTransaksi::latest()->get());

            // create Transaksi Kas
            $kasTransaksi = KasTransaksi::create([
                "id_kas" => $request->id_kas,
                "sumber" => "pengeluaran",
                "id_sumber" => $pengeluaran->id_pengeluaran,
                "tanggal" => now(),
                "keterangan" => "Pengeluaran ke #{$pengeluaran->id_pengeluaran}",
                "debit" => 0,
                "kredit" => $request->nominal,
                "saldo_akhir" => $saldoAkhir,
            ]);

            $kas->update([
                'saldo' => $saldoAkhir
            ]);

            // $this->logActivity(
            //     "create",
            //     "cashflow_pengeluaran",
            //     $pengeluaran->id_pengeluaran,
            //     null,
            //     $pengeluaran->getAttributes(),
            //     "tambah data pengeluaran baru"
            // );
            DB::commit();

            return response()->json([
                'message' => 'Pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran,
                'transaksi' => $kasTransaksi,
                'kas' => $kas
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
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
            'status_tagihan' => 'nullable|string|in:belum_lunas,lunas,menunggu',
            'tanggal_pembuatan_tagihan' => 'nullable|date',
        ]);

        $status = $request->status_tagihan ?? 'belum_lunas';
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

    public function insertTipePembayaran(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tipe_periodik' => 'required|string|in:bulanan,sekali,semester,tahunan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $tipe = TipePembayaran::create([
                'nama_tipe' => $request->nama_tipe,
                'nominal' => $request->nominal,
                'tipe_periodik' => $request->tipe_periodik,
                'keterangan' => $request->keterangan,
            ]);

            // ✅ Automatically create Kas entry
            Kas::create([
                'id_tipe_pembayaran' => $tipe->id_tipe_pembayaran,
                'nama_kas' => $tipe->nama_tipe,
                'saldo' => 0,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tipe Pembayaran & Kas created successfully',
                'data' => $tipe
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function updateTipePembayaran(Request $request, $id)
    {
        $tipe = TipePembayaran::findOrFail($id);

        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'nominal' => 'required|numeric',
            'tipe_periodik' => 'required|string|in:bulanan,sekali,semester,tahunan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $tipe->update([
            'nama_tipe' => $request->nama_tipe,
            'nominal' => $request->nominal,
            'tipe_periodik' => $request->tipe_periodik,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'message' => 'Tipe Pembayaran updated successfully',
            'data' => $tipe
        ], 200);
    }


    public function deleteTipePembayaran($id)
    {
        $tipe = TipePembayaran::findOrFail($id);
        $tipe->delete();

        return response()->json([
            'message' => 'Tipe Pembayaran deleted successfully'
        ], 200);
    }
}
