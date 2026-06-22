<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index()
    {
        $user    = Auth::user();
        $isSuper = $user->role === 'super_admin';

        if ($isSuper) {
            $stats  = $this->dashboard->superAdminStats();
            $audit  = $this->dashboard->superAdminAuditStats();
            $depts  = $this->dashboard->departmentFileCounts();
            $recent = $this->dashboard->superAdminRecentData();

            return view('super_admin.dashboard', [
                'totalFiles'           => $stats['total_files'],
                'totalDepartments'     => $stats['total_departments'],
                'totalUsers'           => $stats['total_users'],
                'pendingTransfers'     => $stats['pending_transfers'],
                'publicSubmissions'    => $stats['public_submissions'],
                'auditStats'           => $audit,
                'departmentFileCounts' => $depts,
                'recentTransfers'      => $recent['recentTransfers'],
                'recentAudit'          => $recent['recentAudit'],
                'pendingRequests'      => $recent['pendingRequests'],
            ]);
        }

        // Admin (department-scoped)
        $deptId = $user->department_id;
        $stats  = $this->dashboard->adminStats($deptId);
        $recent = $this->dashboard->adminRecentData($deptId);

        return view('admin.dashboard', [
            'deptFiles'          => $stats['dept_files'],
            'deptUsers'          => $stats['dept_users'],
            'pendingRequests'    => $stats['pending_requests'],
            'completedTransfers' => $stats['completed_transfers'],
            'recentFiles'        => $recent['recentFiles'],
            'recentActivity'     => $recent['recentActivity'],
            'pendingApprovals'   => $recent['pendingApprovals'],
            'recentUsers'        => $recent['recentUsers'],
        ]);
    }
}
