<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class StaffKeuangan extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    protected $table = 'cashflow_staff_keuangan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama',
        'email',
        'password',
        'status'
    ];

    // optional: biar password tidak ikut ke JSON response
    protected $hidden = [
        'password',
    ];
}
