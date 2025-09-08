<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffKeuangan;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class KeuanganController extends Controller
{
    public function getsiswa(){
        $siswa = DB::table('siswa')->get();

        return $siswa;
    }

    public function LoginKeuangan(Request $request){
        $staff = StaffKeuangan::where('email', $request->email)->first();

        if(! $staff || ! Hash::check($request->password, $staff->password)){
            return response()->json(['message' => 'email atau password salah']);
        }

        //generate token
        $token = $staff->createToken('staff-token')->plainTextToken;

        return response()->json([
        'message' => 'Login berhasil',
        'token'   => $token
        ], 200);
    }

    public function CreateACCKeuangan(){
       DB::beginTransaction();
        try {
            StaffKeuangan::create([
                'nama'     => 'Haikal Risnandar',
                'email'    => 'haikal@example.com',
                'password' => Hash::make('rahasia123'), 
                'status'   => 'Aktif'
            ]);

            DB::commit();
            return response()->json(['message' => 'Akun staff keuangan berhasil dibuat'], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat akun staff keuangan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
