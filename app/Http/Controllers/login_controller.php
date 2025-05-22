<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class login_controller extends Controller
{

    public function loginOrRedirect(Request $request)
    {
        $userID = $request->cookie('userID');
        $userRole = $request->cookie('userRole');

        if ($userID && $userRole) {
            switch ($userRole) {
                case 'siswa':
                    return redirect()->route('info.presensi');
                case 'waliKelas':
                    return redirect()->route('dashboard-wali-kelas');
                case 'guruMapel':
                    return redirect()->route('dashboard.mapel');
            }
        }
        // Belum login, lempar ke landing page
        return redirect()->route('landing')->with("warning", "Keluar dari akun, silahkan lakukan login kembali");
    }

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
        $request->validate([
            'inputUsername' => 'required',
            'inputPassword' => 'required'
        ]);

        // Ambil data dari form
        $username = $request->input('inputUsername');
        $password = $request->input('inputPassword');

        $user = DB::table('siswa')->where('nisn', $username)->first();

        if ($user) {
            return $this->checkhashmd5('info.presensi', 'login-siswa', $password, $user);
        } else {
            return redirect()->route('login-siswa')->with("error", "user tidak ditemukan");
        }
    }

    public function auth_login_walikelas(Request $request)
    {
        $request->validate([
            'inputUsername' => 'required',
            'inputPassword' => 'required'
        ]);

        // Ambil data dari form
        $username = $request->input('inputUsername');
        $password = $request->input('inputPassword');

        $user = DB::table('wali_kelas')->where('nip_wali_kelas', $username)->first();

        if ($user) {
            return $this->checkhashmd5('dashboard-wali-kelas', 'login-walikelas', $password, $user);
        } else {
            return redirect()->route('login-walikelas')->with("error", "user tidak ditemukan");
        }
    }

    public function auth_login_gurumapel(Request $request)
    {
        $request->validate([
            'inputUsername' => 'required',
            'inputPassword' => 'required'
        ]);

        // Ambil data dari form
        $username = $request->input('inputUsername');
        $password = $request->input('inputPassword');

        $user = DB::table('guru_mapel')->where('nip_guru_mapel', $username)->first();

        if ($user) {
            return $this->checkhashmd5('nilai.fetch', 'login-gurumapel', $password, $user);
        } else {
            return redirect()->route('login-gurumapel')->with("error", "user tidak ditemukan");
        }
    }

    public function checkhashmd5($success_login, $error_login, $password, $user)
    {
        if (strlen($user->password) === 32) {
            if (md5($password) === $user->password) {
                if (property_exists($user, 'nisn') && $user->nisn) {
                    $id = $user->nisn;
                    $role = "siswa";
                } elseif (property_exists($user, 'nip_wali_kelas') && $user->nip_wali_kelas) {
                    $id = $user->nip_wali_kelas;
                    $role = "waliKelas";
                } elseif (property_exists($user, 'nip_guru_mapel') && $user->nip_guru_mapel) {
                    $id = $user->nip_guru_mapel;
                    $role = "guruMapel";
                } else {
                    return redirect()->route($error_login)->with("error", "User tidak memiliki identitas yang valid");
                }

                session([
                    'userID' => $id,
                    'userRole' => $role,
                ]);

                return redirect()
                    ->route($success_login)
                    ->withCookies([
                        cookie('userID', $id, 60),
                        cookie('userRole', $role, 60)
                    ])
                    ->with("success", "berhasil Login");
            } else {
                return redirect()->route($error_login)->with("error", "Password salah");
            }
        }
    }

    public function checkbycript()
    {
        //buat bycript ntar
        // if (Hash::check($password, $user->password)) {
        //     // pass cocok
        //     return redirect()->route('login-siswa')->with("success", "berhasil Login");
        // } else {
        //     return redirect()->route('login-siswa')->with("success", "berhasil Login");
        // }
    }
}
