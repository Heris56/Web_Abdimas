<?php

use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::view('/', 'landing-page')->name('landing');
Route::view('/login', 'login-page')->name('login');
Route::view('/main-page', 'main-page')->name('main');
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');
Route::view('/info-nilai-siswa', 'info-nilai-siswa')->name('info.nilai');
Route::view('/info-presensi-siswa', 'info-presensi-siswa')->name('info.presensi');