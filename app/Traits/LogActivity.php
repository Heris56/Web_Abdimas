<?php

namespace App\Traits;

use App\Models\ActivityLogs;
use Illuminate\Support\Facades\Auth;

trait LogActivity{
    /**
     * Log Aktivitas User
     * 
     * @param string $action (create/update/delete/login/etc)
     * @param string $tableName (nama tabel yang dimodifikasi)
     * @param int|null $recordId (ID record yg berubah)
     * @param array|null $oldValues (sebelum update/delete)
     * @param array|null $newValues (sesudah create/update)
     * @param string|null $description (penjelasan tambahan)
     */
    public function logActivity(
        string $action,
        string $tableName,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ){
        ActivityLogs::create(
            [
                "user_id" => Auth("sanctum")->id(),
                "action" => $action,
                "table_name" => $tableName,
                "record_id" => $recordId,
                "old_values" => $oldValues,
                "new_values" => $newValues,
                "description" => $description,
                "ip_address" => request()->ip(),
                "user_agent" => request()->userAgent(),
            ]
        );
    }
}