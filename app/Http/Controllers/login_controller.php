<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class login_controller extends Controller
{
    public function getkelas()
    {
        $data = DB::table('kelas')->get();
        return $data;
    }

    public function getsiswa()
    {
        $data = DB::table('siswa')->get();
        return $data;
    }

    public function auth_login_siswa(Request $request)
    {


        // Tampilkan (sementara) untuk cek
        $request->validate([
            'inputUsername' => 'required',
            'inputPassword' => 'required'
        ]);

        // Ambil data dari form
        $username = $request->input('inputUsername');
        $password = $request->input('inputPassword');

        $user = DB::table('siswa')->where('nisn', $username)->first();

        if (!$user) {
            return redirect()->route('login-siswa')->with("error", "user tidak ditemukan");
        }

        if ($user) {
            if (strlen($user->password) === 32) {
                if (md5($password) === $user->password) {
                    return redirect()->route('login-siswa')->with("success", "berhasil Login");
                } else {
                    return redirect()->route('login-siswa')->with("error", "Password salah");
                }
            }
            //buat bycript ntar
            // if (Hash::check($password, $user->password)) {
            //     // pass cocok
            //     return redirect()->route('login-siswa')->with("success", "berhasil Login");
            // } else {
            //     return redirect()->route('login-siswa')->with("success", "berhasil Login");
            // }
        }
    }
}
