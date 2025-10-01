<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kas extends Model
{
    use SoftDeletes;
    protected $table = 'cashflow_kas';
    protected $primaryKey = "id_kas";
    protected $fillable = [
        "id_tipe_pembayaran",
        "nama_kas",
        "saldo"
    ];
}
