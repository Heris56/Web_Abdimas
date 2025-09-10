<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipePembayaran extends Model
{
    use SoftDeletes;
    protected $table = 'cashflow_tipe_pembayaran';
    protected $primaryKey = "id_tipe_pembayaran";
    protected $fillable = [
        'nama_tipe',
        'is_bulanan',
        'is_sekali_bayar',
        'is_pertaun',
        'is_persemester',
        'keterangan',
    ];
}
