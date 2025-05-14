<?php

use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::view('/', 'landing-page')->name('landing');
Route::view('/login', 'login-page')->name('login');
Route::view('/main-page', 'main-page')->name('main');
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');
Route::view('/dashboard-wali-kelas', 'dashboard-wali-kelas')->name('dashboard-wali-kelas');