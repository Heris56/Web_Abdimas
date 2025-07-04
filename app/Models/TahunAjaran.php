<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';

    protected $fillable = [
        'tahun',
        'is_current',
    ];

    // Jika id_tahun_ajaran bukan string
    // public $incrementing = true; // Default true, tidak perlu ditulis jika memang auto-increment integer
    // protected $keyType = 'int'; // Default int, tidak perlu ditulis jika memang int

    // Cast is_current ke boolean
    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Scope untuk mendapatkan tahun ajaran yang sedang aktif.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
