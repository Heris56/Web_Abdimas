<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'cashflow_pengeluaran';
    protected $primaryKey = "id_pengeluaran";
    protected $fillable = [
        'nominal',
        'keterangan',
        'tanggal',
        'id_kas',
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function tipe_kas(){
        return $this->belongsTo(TipePembayaran::class, 'id_kas', 'id_tipe_pembayaran');
    }

}
