<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeuanganController;
use function Pest\Laravel\json;


Route::middleware(['auth:sanctum'])->group(function () {
    // test
    Route::get('/testapi', function (Request $request) {
        return response()->json([
            "message" => "Test Data Ganti"
        ], 200);
    });
    route::prefix("test")->group(function(){
        Route::post("/createKasDefault", [KeuanganController::class, "createKasDefault"]); // untuk test dan pastikan semua kas konek ke tipe pembayaran
        Route::post("/ResetAllKas", [KeuanganController::class, "ResetAllKas"]);
    });
        
    // Pembayaran
    Route::get('/datapembayaran', [KeuanganController::class, 'dataPembayaran'])->name('api.datapembayaran');
    Route::post('/insertpembayaran', [KeuanganController::class, 'insertPembayaran'])->name('api.addpembayaran');
    Route::post('/createPembayaran', [KeuanganController::class, "createPembayaran"]);
    Route::get('/getpembayaran', [KeuanganController::class, 'getPembayaran'])->name('api.Pembayaran');

    // Pengeluaran
    Route::get('/datapengeluaran', [KeuanganController::class, 'dataPengeluaran'])->name('api.datapengeluaran');
    Route::post('/insertpengeluaran', [KeuanganController::class, 'insertPengeluaran'])->name('api.addpengeluaran');

    // Tagihan
    Route::post('/inserttagihan', [KeuanganController::class, 'inputTagihanAllSiswa'])->name('api.inputtagihanallsiswa');
    Route::get('/getTagihanSiswa', [KeuanganController::class, "TagihanSiswa"]);
    Route::post('/createTagihanSiswa', [KeuanganController::class, "createTagihan"]);

    //Route Tipe Pembayaran
    Route::get('/gettipepembayaran', [KeuanganController::class, 'getTipePembayaran'])->name('api.TipePembayaran');
    Route::post('/inserttipepembayaran', [KeuanganController::class, 'insertTipePembayaran'])->name('api.inserttipepembayaran');
    Route::put('/updatetipepembayaran/{id}', [KeuanganController::class, 'updateTipePembayaran'])->name('api.updatetipepembayaran');
    Route::delete('/deletetipepembayaran/{id}', [KeuanganController::class, 'deleteTipePembayaran'])->name('api.deletepengeluaran');

    // kas
    Route::get("/getKas", [KeuanganController::class, "getKas"]);
    Route::get("/TransaksiKas", [KeuanganController::class, "getTransaksiKas"]);

    // Logs
    Route::get('/getlogs', [KeuanganController::class, 'dataLog'])->name('api.dataLog');

    // misc
    Route::get('/getsiswa', [KeuanganController::class, 'getsiswa'])->name('api.siswa');
    Route::get('/getTahunAjaran', [KeuanganController::class, "getTahunAjaran"]);
    Route::post('/logoutstaff', [KeuanganController::class, 'LogoutKeuangan'])->name('api.logoutstaffKeuangan');
    Route::post('/passchangestaff', [KeuanganController::class, 'ChangePassword'])->name('api.passchangestaffKeuangan');
    Route::get('/getProfile', [KeuanganController::class, 'getProfile']);

});

Route::post('/addstaff', [KeuanganController::class, 'CreateACCKeuangan'])->name('api.createstaff');
Route::post('/loginstaff', [KeuanganController::class, 'LoginKeuangan'])->name('api.loginstaffKeuangan');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
