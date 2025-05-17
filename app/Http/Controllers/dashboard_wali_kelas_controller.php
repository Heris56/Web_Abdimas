<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class dashboard_wali_kelas_controller extends Controller
{
    public function get_wali_kelas_by_nip(){
        $data = DB::table('wali_kelas')->get();
        return $data;
    }
}
