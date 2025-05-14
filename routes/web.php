<?php

use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::view('/', 'landing-page')->name('landing');
Route::view('/loginsiswa', 'login-page-siswa')->name('login-siswa');
Route::view('/loginwalikelas', 'login-page-walikelas')->name('login-walikelas');
Route::view('/logingurumapel', 'login-page-gurumapel')->name('login-gurumapel');
Route::view('/main-page', 'main-page')->name('main');
Route::view('/cari-data-siswa', 'cari-data-siswa')->name('cari');
