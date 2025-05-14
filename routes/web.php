<?php

use App\Http\Controllers\login_controller;
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
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');
Route::view('/info-nilai-siswa', 'info-nilai-siswa')->name('info.nilai');
Route::view('/info-presensi-siswa', 'info-presensi-siswa')->name('info.presensi');



Route::get('/test-db', function () {
    $data = DB::table('kelas')->get();
    return $data;
});

Route::post('/login/siswa', [login_controller::class, 'auth_login_siswa'])->name('login.siswa');

Route::get('/api/kelas', [login_controller::class, 'getkelas'])->name('getkelas');
Route::get('/api/siswa', [login_controller::class, 'getsiswa'])->name('getsiswa');
Route::view('/dashboard-wali-kelas', 'dashboard-wali-kelas')->name('dashboard-wali-kelas');
