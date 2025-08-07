<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeuanganController;

Route::get('/getsiswa', [KeuanganController::class,'getsiswa'])->name('api.siswa');

Route::get('/testapi', function (Request $request) {
    return 'test api';
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
