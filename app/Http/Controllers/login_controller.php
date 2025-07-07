<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

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

    public function auth_login_staff(Request $request)
    {
        $request->validate([
            'staffNIP' => 'required|max:20',
            'staffemail' => 'required|max:255',
            'staffpassword' => 'required|max:20'
        ]);

        $NIP = $request->input('staffNIP');
        $email = $request->input('staffemail');
        $password = $request->input('staffpassword');

        $user = DB::table('staff')->where('nip_staff', $NIP)->where('email', $email)->first();
        if ($user) {
            return $this->checkhashmd5('data.fetch', 'login-staff', $password, $user);
        } else {
            return redirect()->route('login-staff')->with("error", "user tidak ditemukan");
        }
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

        // ambil pass saat ini (either masih md5 atau nggak)
        $storedPassword = $user->password;
        $isAuthenticated = false;

        if (strlen($user->password) === 32) {
            if (md5($password) === $storedPassword) {
                $isAuthenticated = true;

                //pass udah cocok, buatkan pass versi bycript
                $newHashedPassword = Hash::make($password);
                // dd($newHashedPassword);

                $tablename = '';
                if (property_exists($user, 'nisn')) {
                    $tablename = 'siswa';
                    $idColumn = 'nisn';
                } elseif (property_exists($user, 'nip_wali_kelas')) {
                    $tablename = 'wali_kelas';
                    $idColumn = "nip_wali_kelas";
                } elseif (property_exists($user, 'nip_guru_mapel')) {
                    $tablename = 'guru_mapel';
                    $idColumn = "nip_guru_mapel";
                } elseif (property_exists($user, 'nip_staff')) {
                    $tablename = 'staff';
                    $idColumn = "nip_staff";
                }

                if ($tablename && isset($idColumn)) {
                    DB::table($tablename)
                        ->where($idColumn, $user->$idColumn)
                        ->update(['password' => $newHashedPassword]);
                }
            }
        } else {
            // Ini diasumsikan sebagai hash bcrypt atau format lain yang sudah aman
            if (Hash::check($password, $storedPassword)) {
                $isAuthenticated = true;
            }
        }

        if ($isAuthenticated) {
            //pass udah cocok
            if (property_exists($user, 'nisn') && $user->nisn) {
                $id = $user->nisn;
                $role = "siswa";
                $username = $user->nama_siswa;
            } elseif (property_exists($user, 'nip_wali_kelas') && $user->nip_wali_kelas) {
                $id = $user->nip_wali_kelas;
                $role = "waliKelas";
                $username = $user->nama;
            } elseif (property_exists($user, 'nip_guru_mapel') && $user->nip_guru_mapel) {
                $id = $user->nip_guru_mapel;
                $role = "guruMapel";
                $username = $user->nama_guru;
            } elseif (property_exists($user, 'nip_staff') && $user->nip_staff) {
                $id = $user->nip_staff;
                $role = "Staff";
                $username = $user->nama_staff;
            } else {
                return redirect()->route($error_login)->with("error", "User tidak memiliki identitas yang valid");
            }
            session([
                'userID' => $id, // id user
                'userRole' => $role, // untuk role user
                'username' => $username // simpan nama user ke session untuk navbar
            ]);

            return redirect()
                ->route($success_login)
                ->withCookies([
                    cookie('userID', $id, 60), // :1 == test ? >1 == final
                    cookie('userRole', $role, 60) // :1 == test ? >1 == final
                ])
                ->with("success", "berhasil Login");
        } else {
            return redirect()->route($error_login)->with("error", "Password salah");
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

    public function logout()
    {
        session()->forget('username');
        session()->flush();
        Cookie::queue(Cookie::forget('userID'));
        Cookie::queue(Cookie::forget('userRole'));
        return redirect()->route('landing')
            ->with("success", "Berhasil Logout");
    }
}
