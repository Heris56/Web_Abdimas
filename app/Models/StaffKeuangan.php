<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class StaffKeuangan extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    use HasFactory;
    protected $table = 'cashflow_staff_keuangan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama',
        'email',
        'password',
        'status',
        'role'
    ];

    // optional: biar password tidak ikut ke JSON response
    protected $hidden = [
        'password',
    ];
}
