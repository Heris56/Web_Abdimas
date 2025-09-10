<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use SoftDeletes;
    protected $table = 'cashflow_tagihan';
    protected $primaryKey = "id_pembayaran";
    protected $fillable = [
        'status_pembayaran',
        'tanggal_pembayaran',
        'nisn',
        'id_tipe_pembayaran'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
