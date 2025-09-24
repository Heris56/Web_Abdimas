<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'cashflow_tagihan';
    protected $primaryKey = "id_pembayaran";
    protected $fillable = [
        'status_pembayaran',
        'tanggal_pembuatan_tagihan',
        'nisn',
        'jadwal_pembayaran',
        'id_tipe_pembayaran'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }

    public function tipePembayaran()
    {
        return $this->belongsTo(TipePembayaran::class, 'id_tipe_pembayaran', 'id_tipe_pembayaran');
    }
}
