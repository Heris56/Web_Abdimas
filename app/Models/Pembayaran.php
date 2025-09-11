<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;
    protected $table = 'cashflow_tagihan_pembayaran';
    protected $primaryKey = "id_tagihan_pembayaran";
    protected $fillable = [
        'jumlah_pembayaran',
        'id_pembayaran',
    ];
    protected $hidden = [
        'deleted_at'
    ];
    public function Tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_pembayaran', 'id_pembayaran');
    }


}
