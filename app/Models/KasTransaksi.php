<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KasTransaksi extends Model
{
    use HasFactory;
    protected $table = 'cashflow_kas_transaksi';
    protected $primaryKey = "id_kas_transaksi";
    protected $fillable = [
        "id_kas",
        "sumber",
        "id_sumber",
        "tanggal",
        "keterangan",
        "debit",
        "kredit",
        "saldo_akhir"
    ];

    public function kas(){
        return $this->belongsTo(Kas::class, "id_kas", "id_kas");
    }
}
