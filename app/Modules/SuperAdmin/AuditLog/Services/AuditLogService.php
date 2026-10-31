<?php

namespace App\Modules\SuperAdmin\AuditLog\Services;

use Carbon\Carbon;
use App\Modules\SuperAdmin\AuditLog\Repositories\AuditLogRepository;

class AuditLogService
{
    public function __construct(
        protected AuditLogRepository $auditLogRepository
    ) {}

    public function getDashboardData(array $filters = []): array
    {
        $query = $this->auditLogRepository->getBaseQuery();

        // Filter Kategori
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        // Filter Rentang Waktu
        $range = $filters['range'] ?? '14';
        if ($range === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === '7') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($range === '14') {
            $query->where('created_at', '>=', Carbon::now()->subDays(14));
        } elseif ($range === '30') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        // Pencarian Teks
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('instansi_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Data Stat Cards via Repository
        $counts = $this->auditLogRepository->getCountsByCategory();
        $totalLogsCount = $counts['total'];
        $authEventsCount = $counts['auth'];
        $billingEventsCount = $counts['billing'];
        $sensitiveActionsCount = $counts['sensitive'];

        // Data Line Chart Tren 14 Hari
        $trendData = $this->auditLogRepository->getTimelineData(14);
        $trendLabels = $trendData->pluck('log_date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();
        $trendCounts = $trendData->pluck('total')->toArray();

        // Data Doughnut Chart Distribusi Kategori
        $pieLabels = ['Autentikasi', 'Billing / Lisensi', 'Pengguna', 'Fitur & Maintenance', 'Sistem & Keamanan'];
        $pieData = [
            $counts['auth'],
            $counts['billing'],
            \App\Models\AuditLog::where('category', 'user')->count(),
            \App\Models\AuditLog::whereIn('category', ['module', 'maintenance'])->count(),
            \App\Models\AuditLog::whereNotIn('category', ['auth', 'billing', 'user', 'module', 'maintenance'])->count(),
        ];
        $pieColors = ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#64748b'];

        return compact(
            'logs',
            'totalLogsCount',
            'authEventsCount',
            'billingEventsCount',
            'sensitiveActionsCount',
            'trendLabels',
            'trendCounts',
            'pieLabels',
            'pieData',
            'pieColors'
        );
    }
}
