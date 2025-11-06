<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'siswa';
    protected $primaryKey = "nisn";
    protected $fillable = [
        'nisn',
        'nama_siswa',
        'password',
        'status',
        'tahun_ajaran',
        'id_kelas',
    ];
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
