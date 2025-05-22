<?php

use App\Http\Controllers\login_controller;
use App\Http\Controllers\controllerSiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\dashboard_wali_kelas_controller;
use App\Http\Controllers\DataController;
use App\Http\Controllers\NilaiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::view('/', 'landing-page')->name('landing');
Route::view('/loginsiswa', 'login-page-siswa')->name('login-siswa');
Route::view('/loginwalikelas', 'login-page-walikelas')->name('login-walikelas');
Route::view('/logingurumapel', 'login-page-gurumapel')->name('login-gurumapel');
Route::view('/main-page', 'main-page')->name('main');
Route::view('/dashboard/walikelas', 'dashboard-wali-kelas')->name('dashboard.walikelas');
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');
Route::view('/info-nilai-siswa', 'info-nilai-siswa')->name('info.nilai');
Route::view('/info-presensi-siswa', 'info-presensi-siswa')->name('info.presensi');

Route::post('/login/siswa', [login_controller::class, 'auth_login_siswa'])->name('login.siswa');
Route::post('/login/gurumapel', [login_controller::class, 'auth_login_gurumapel'])->name('login.gurumapel');
Route::post('/login/walikelas', [login_controller::class, 'auth_login_walikelas'])->name('login.walikelas');
Route::get('/api/kelas', [login_controller::class, 'getkelas'])->name('getkelas');
Route::get('/api/siswa', [login_controller::class, 'getsiswa'])->name('getsiswa');
Route::get('/dashboard-wali-kelas', [dashboard_wali_kelas_controller::class, 'get_wali_kelas_by_nip'])->name('dashboard-wali-kelas');

//Controllersiswa
Route::get('/presensi', [controllerSiswa::class, 'showPresensi'])->name('presensi');
Route::get('/presensi/{nisn}', [controllerSiswa::class, 'getHistorySiswa'])->name('presensi.nisn');
Route::get('/info-presensi-siswa', [controllerSiswa::class, 'showPresensi'])->name('info.presensi');
Route::get('/dashboard-siswa', [controllerSiswa::class, 'get_absen_by_nisn'])->name('dashboard.siswa');
Route::get('/nilai-siswa', [controllerSiswa::class, 'fetchNilaiSiswa'])->name('info.nilai');
Route::get('/data-siswa', [controllerSiswa::class, 'fetchdatasiswa'])->name('data.siswa');

// dashboard/guru-mapel
// Route::view('/dashboard-guru-mapel', 'dashboard-guru-mapel')->name('dashboard.mapel');
Route::get('/dashboard/guru-mapel', [NilaiController::class, 'fetchNilai'])->name('nilai.fetch');
Route::post('/dashboard/guru-mapel/input-nilai', [NilaiController::class, 'inputNilai'])->name('nilai.input');
Route::post('/dashboard/guru-mapel/update-nilai', [NilaiController::class, 'updateNilai'])->name('nilai.update');

// staff
Route::view('/dashboard-staff', 'dashboard-staff')->name('dashboard.staff');
Route::get('/dashboard-staff/data/{type}', [DataController::class, 'fetchData'])->name('data.fetch');


// ini buat test <<<<<<<<<<---------->>>>>>>>>>
Route::get('/test/login', [login_controller::class, 'checkhashmd5']);
Route::get('/test/cookies', [login_controller::class, 'loginOrRedirect']);
Route::get('/test-db', function () {
    $data = DB::table('guru_mapel')->get();
    return $data;
});

Route::get('/testcookies', function (Request $request) {
    return dd([
        'userID' => $request->cookie('userID'),
        'userRole' => $request->cookie('userRole'),
        'allCookies' => $request->cookies->all()
    ]);
});
