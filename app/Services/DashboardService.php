<?php

namespace App\Services;

use App\Models\Department;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    // Cache TTL in seconds (5 minutes)
    private const TTL = 300;

    /* ──────────────────────────────────────────────────────────────
     *  SUPER ADMIN — system-wide cached stats
     * ──────────────────────────────────────────────────────────── */
    public function superAdminStats(): array
    {
        return Cache::remember('sa_stats', self::TTL, fn() => [
            'total_files'       => FileRecord::count(),
            'total_departments' => Department::count(),
            'total_users'       => User::count(),
            'total_transfers'   => FileTransfer::count(),
            'total_admins'      => User::where('role', 'admin')->count(),
        ]);
    }

    public function superAdminMovementStats(): array
    {
        return Cache::remember('sa_movement_stats', self::TTL, function () {
            $actions = ['created', 'transferred'];
            $result  = [];
            foreach ($actions as $action) {
                $result[$action] = FileMovement::where('action', $action)->count();
            }
            return $result;
        });
    }

    public function departmentFileCounts(): object
    {
        return Cache::remember(
            'dept_file_counts',
            self::TTL,
            fn() => Department::withCount('files')->orderByDesc('files_count')->get()
        );
    }

    public function superAdminRecentData(): array
    {
        return [
            'recentTransfers' => FileTransfer::with(['sender', 'receiver', 'file.department'])
                ->latest()->take(10)->get(),
            'recentMovements' => FileMovement::with(['file', 'fromUser', 'toUser', 'fromDept', 'toDept'])
                ->latest()->take(10)->get(),
        ];
    }

    /* ──────────────────────────────────────────────────────────────
     *  ADMIN — department-scoped cached stats
     * ──────────────────────────────────────────────────────────── */
    public function adminStats(int $deptId): array
    {
        return Cache::remember("admin_stats_{$deptId}", self::TTL, fn() => [
            'dept_files'          => FileRecord::where('department_id', $deptId)->count(),
            'dept_users'          => User::where('department_id', $deptId)->count(),
            'total_transfers'     => FileMovement::where('from_department', $deptId)
                ->orWhere('to_department', $deptId)
                ->where('action', 'transferred')
                ->count(),
        ]);
    }

    public function adminRecentData(int $deptId): array
    {
        return [
            'recentFiles'    => FileRecord::with(['currentHolder', 'creator'])
                ->where('department_id', $deptId)->latest()->take(7)->get(),
            'recentActivity' => FileMovement::with(['file', 'fromUser', 'toUser', 'fromDept', 'toDept'])
                ->where(fn($q) => $q->where('from_department', $deptId)->orWhere('to_department', $deptId))
                ->latest()->take(8)->get(),
            'recentUsers'    => User::with('designation')
                ->where('department_id', $deptId)->latest()->take(5)->get(),
        ];
    }

    /* ──────────────────────────────────────────────────────────────
     *  USER — personal cached stats
     * ──────────────────────────────────────────────────────────── */
    public function userStats(int $userId): array
    {
        return Cache::remember("user_stats_{$userId}", self::TTL, fn() => [
            'total_my_files'  => FileRecord::where(fn($q) =>
                $q->where('created_by', $userId)->orWhere('current_user_id', $userId))->count(),
            'sent_files'      => FileMovement::where('from_user', $userId)->where('action', 'transferred')->count(),
            'received_files'  => FileMovement::where('to_user', $userId)->where('action', 'transferred')->count(),
        ]);
    }

    /* ──────────────────────────────────────────────────────────────
     *  CACHE INVALIDATION — call after writes
     * ──────────────────────────────────────────────────────────── */
    public static function clearSuperAdminCache(): void
    {
        Cache::forget('sa_stats');
        Cache::forget('sa_movement_stats');
        Cache::forget('dept_file_counts');
    }

    public static function clearAdminCache(int $deptId): void
    {
        Cache::forget("admin_stats_{$deptId}");
    }

    public static function clearUserCache(int $userId): void
    {
        Cache::forget("user_stats_{$userId}");
    }
}
