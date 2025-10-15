<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'cashflow_tagihan';
    protected $primaryKey = "id_tagihan";
    protected $fillable = [
        'status_tagihan',
        'tanggal_pembuatan_tagihan',
        'nisn',
        'periode',
        'id_tipe_pembayaran',
        'id_tahun_ajaran',
        'nominal_tagihan',
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

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran');
    }
}
