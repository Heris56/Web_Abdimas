<?php

use App\Http\Controllers\login_controller;
use App\Http\Controllers\controllerSiswa;
use App\Http\Middleware\isTestMode;
use App\Http\Middleware\RestrictAdminIP;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Http\Controllers\dashboard_wali_kelas_controller;
use App\Http\Controllers\DataController;
use App\Http\Controllers\NilaiController;
use App\Http\Middleware\RedirectIfLoggedIn;
use App\Http\Middleware\CheckLoginCookie;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Route::get('/welcome', function () {
//     return view('welcome');
// });

// semua view
Route::view('/', 'landing-page')->name('landing')->middleware(RedirectIfLoggedIn::class);
Route::view('/loginsiswa', 'login-page-siswa')->name('login-siswa');
Route::view('/loginwalikelas', 'login-page-walikelas')->name('login-walikelas');
Route::view('/logingurumapel', 'login-page-gurumapel')->name('login-gurumapel');
Route::view('/main-page', 'main-page')->name('main');
Route::view('/dashboard/walikelas', 'dashboard-wali-kelas')->name('dashboard.walikelas');
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');

// Guest access routes
Route::get('/guest/info/siswa', [controllerSiswa::class, 'showGuestInfoSiswa'])->name('guest.info.siswa');
// ADD THIS NEW LINE FOR POST SUBMISSIONS
Route::post('/guest/info/siswa', [controllerSiswa::class, 'showGuestInfoSiswa'])->name('guest.info.siswa.post');

// view all user (Authenticated routes)
Route::middleware([CheckLoginCookie::class . ':siswa'])->group(function () {
    Route::get('/info-presensi-siswa', [controllerSiswa::class, 'showPresensi'])->name('info.presensi');
    Route::get('/info/nilai', [controllerSiswa::class, 'fetchNilaiSiswa'])->name('info.nilai');
    // These routes are moved here to be protected by the authentication middleware
    Route::get('/siswa/ganti-password', [controllerSiswa::class, 'formGantiPassword'])->name('siswa.formGantiPassword');
    Route::put('/siswa/update-password', [controllerSiswa::class, 'updatePassword'])->name('siswa.updatePassword');
});

Route::middleware([CheckLoginCookie::class . ':waliKelas'])->group(function () {
    Route::get('/dashboard-wali-kelas', [dashboard_wali_kelas_controller::class, 'get_wali_kelas_by_nip'])->name('dashboard-wali-kelas');
    Route::get('/dashboard/ganti-password', [dashboard_wali_kelas_controller::class, 'formGantiPassword'])->name('dashboard.walikelas.ganti-password');
    Route::post('/dashboard/walikelas/add-tanggal', [dashboard_wali_kelas_controller::class, 'add_tanggal'])->name('dashboard.walikelas.add-tanggal');
    Route::post('/dashboard/walikelas/edit-kehadiran', [dashboard_wali_kelas_controller::class, 'edit_kehadiran'])->name('dashboard.walikelas.edit-kehadiran');
    Route::delete('/dashboard/walikelas/delete_tanggal/{tanggal}', [dashboard_wali_kelas_controller::class, 'delete_tanggal'])->name('dashboard.walikelas.delete-tanggal');
    Route::put('/dashboard/walikelas/update-password', [dashboard_wali_kelas_controller::class, 'updatePassword'])->name('gantiPassword.walikelas');
    Route::post('/dashboard/walikelas/edit-kehadiran', [dashboard_wali_kelas_controller::class, 'edit_kehadiran'])->name('dashboard.walikelas.edit-kehadiran');
});
Route::middleware([CheckLoginCookie::class . ':guruMapel'])->group(function () {
    Route::get('/dashboard/guru-mapel', [NilaiController::class, 'fetchNilai'])->name('nilai.fetch');
});

// func login
Route::post('/login/siswa', [login_controller::class, 'auth_login_siswa'])->name('login.siswa');
Route::post('/login/gurumapel', [login_controller::class, 'auth_login_gurumapel'])->name('login.gurumapel');
Route::post('/login/walikelas', [login_controller::class, 'auth_login_walikelas'])->name('login.walikelas');
Route::get('/api/kelas', [login_controller::class, 'getkelas'])->name('getkelas');
Route::get('/api/siswa', [login_controller::class, 'getsiswa'])->name('getsiswa');

// logout
Route::post('/logout', [login_controller::class, 'logout'])->name('logout');



// dashboard/guru-mapel
// Route::view('/dashboard-guru-mapel', 'dashboard-guru-mapel')->name('dashboard.mapel');
Route::post('/dashboard/guru-mapel/input-nilai', [NilaiController::class, 'inputNilai'])->name('nilai.input');
Route::post('/dashboard/guru-mapel/update-nilai', [NilaiController::class, 'updateNilai'])->name('nilai.update');
Route::post('/dashboard/guru-mapel/tambah-kegiatan', [NilaiController::class, 'tambahKegiatan'])->name('nilai.tambah-kegiatan');

// staff
Route::view('/login/staff', 'login-page-staff')->name('login-staff');
Route::post('/login/staff', [login_controller::class, 'auth_login_staff'])->name('auth.staff');
Route::middleware([RestrictAdminIP::class, CheckLoginCookie::class . ':Staff'])->group(function () {
    // Route::view('/dashboard-staff', 'dashboard-staff')->name('dashboard.staff');
    Route::get('/dashboard/staff/data/{type?}', [DataController::class, 'fetchData'])->name('data.fetch');
    Route::post('dashboard/staff/data/input/{type}', [DataController::class, 'inputData'])->name('data.input');
    Route::put('/dashboard/staff/data/update/{type}/{id}', [DataController::class, 'updateData'])->name('data.update');
    Route::post('/confirmpassword', [DataController::class, 'ConfirmPassword'])->name('data.confirmpassword');
});

// ini buat test <<<<<<<<<<---------->>>>>>>>>>
Route::middleware([isTestMode::class])->group(function () {
    Route::get('/test/login', [login_controller::class, 'checkhashmd5']);
    Route::get('/test/cookies', [login_controller::class, 'loginOrRedirect']);
    Route::get('/test-db', function () {
        $data = DB::table('guru')->get();
        return $data;
    });

    Route::get('/test/clear-cookies', function () {
        Cookie::queue(Cookie::forget('userID'));
        Cookie::queue(Cookie::forget('userRole'));
        session()->forget(['userID', 'userRole']);
        return "Cookies cleared.";
    });

    Route::get("/test/getTahun", function () {
        $data = TahunAjaran::all();
        return response()->json($data);
    });

    Route::get('/testcookies', function (Request $request) {
        return dd([
            'userID' => $request->cookie('userID'),
            'userRole' => $request->cookie('userRole'),
            'allCookies' => $request->cookies->all()
        ]);
    });
});


// Route::get('/info-presensi-siswa', [controllerSiswa::class, 'showPresensi'])->name('info.presensi')->middleware(CheckLoginCookie::class);
