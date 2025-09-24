<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $table = 'cashflow_activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(StaffKeuangan::class, 'user_id', 'id');
    }
}
