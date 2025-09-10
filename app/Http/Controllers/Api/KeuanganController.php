<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\StaffKeuangan;
use App\Models\TipePembayaran;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class KeuanganController extends Controller
{
    public function getsiswa(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $siswa = Siswa::paginate($perPage);

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
        $pembayaran = Tagihan::with('siswa')->get();

        return response()->json([
            "message" => "Berhasil Fetch data Pembayaran",
            "data" => $pembayaran,
            
        ], 200);
    } 
    public function getTipePembayaran(Request $request)
    {
        $tipePembayaran = TipePembayaran::all();

        return response()->json([
            "message" => "Berhasil Fetch data Tipe Pembayaran",
            "data" => $tipePembayaran,
            
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
}
