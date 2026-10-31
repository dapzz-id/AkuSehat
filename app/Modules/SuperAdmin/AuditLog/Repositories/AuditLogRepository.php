<?php

namespace App\Modules\SuperAdmin\AuditLog\Repositories;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AuditLogRepository
{
    public function getBaseQuery(): Builder
    {
        return AuditLog::query()->orderBy('created_at', 'desc');
    }

    public function getCountsByCategory(): array
    {
        $stats = AuditLog::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN category = 'auth' THEN 1 ELSE 0 END) as `auth`,
            SUM(CASE WHEN category = 'billing' THEN 1 ELSE 0 END) as `billing`,
            SUM(CASE WHEN category IN ('user', 'maintenance', 'module') THEN 1 ELSE 0 END) as `sensitive`
        ")->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'auth' => (int) ($stats->auth ?? 0),
            'billing' => (int) ($stats->billing ?? 0),
            'sensitive' => (int) ($stats->sensitive ?? 0),
        ];
    }

    public function getTimelineData(int $days = 14)
    {
        return AuditLog::selectRaw('DATE(created_at) as log_date, count(*) as total')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('log_date')
            ->orderBy('log_date', 'asc')
            ->get();
    }
}
