<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function getsiswa(){
        $siswa = DB::table('siswa')->get();

        return $siswa;
    }
}
