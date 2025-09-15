<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use SoftDeletes;
    protected $table = 'cashflow_pengeluaran';
    protected $primaryKey = "id_pengeluaran";
    protected $fillable = [
        'nominal',
        'keterangan',
        'tanggal',
    ];
    protected $hidden = [
        'deleted_at'
    ];
    public function Tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_pembayaran', 'id_pembayaran');
    }


}
