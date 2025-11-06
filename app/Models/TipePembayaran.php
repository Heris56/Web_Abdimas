<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipePembayaran extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'cashflow_tipe_pembayaran';
    protected $primaryKey = "id_tipe_pembayaran";
    protected $fillable = [
        'nama_tipe',
        'tipe_periodik',
        'is_cicilable',
        'keterangan',
        'nominal',
    ];
}
