<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\PublicFile;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $isSuper = $user->role === 'super_admin';
        $deptId  = $user->department_id;

        if ($isSuper) {
            return $this->superAdminDashboard();
        }

        return $this->adminDashboard($deptId);
    }

    /* ------------------------------------------------------------------ */
    /*  SUPER ADMIN DASHBOARD                                               */
    /* ------------------------------------------------------------------ */
    private function superAdminDashboard()
    {
        $totalFiles       = FileRecord::count();
        $totalDepartments = Department::count();
        $totalUsers       = User::count();
        $pendingTransfers = TransferRequest::where('status', 'pending')->count();
        $publicSubmissions = PublicFile::count();

        // Audit statistics (action breakdown)
        $auditStats = [
            'created'     => FileMovement::where('action', 'created')->count(),
            'requested'   => FileMovement::where('action', 'requested')->count(),
            'approved'    => FileMovement::where('action', 'approved')->count(),
            'rejected'    => FileMovement::where('action', 'rejected')->count(),
            'transferred' => FileMovement::where('action', 'transferred')->count(),
        ];

        // Recent transfers (all departments — monitor view)
        $recentTransfers = FileTransfer::with(['sender', 'receiver', 'file.department'])
            ->latest()->take(8)->get();

        // Files per department chart data
        $departmentFileCounts = Department::withCount('files')
            ->orderByDesc('files_count')->get();

        // Recent audit movements
        $recentAudit = FileMovement::with(['file', 'fromUser', 'toUser', 'fromDept', 'toDept'])
            ->latest()->take(8)->get();

        // All pending transfer requests (read-only for super admin)
        $pendingRequests = TransferRequest::with(['file', 'sender', 'receiver', 'fromDept', 'toDept'])
            ->where('status', 'pending')->latest()->get();

        return view('super_admin.dashboard', compact(
            'totalFiles', 'totalDepartments', 'totalUsers',
            'pendingTransfers', 'publicSubmissions',
            'auditStats', 'recentTransfers',
            'departmentFileCounts', 'recentAudit',
            'pendingRequests'
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  ADMIN DASHBOARD (department-scoped)                                 */
    /* ------------------------------------------------------------------ */
    private function adminDashboard($deptId)
    {
        $deptFiles       = FileRecord::where('department_id', $deptId)->count();
        $deptUsers       = User::where('department_id', $deptId)->count();
        $pendingRequests = TransferRequest::where('status', 'pending')
            ->where('to_department', $deptId)->count();
        $completedTransfers = TransferRequest::where('status', 'approved')
            ->where('to_department', $deptId)->count();

        // Recent files in department
        $recentFiles = FileRecord::with(['currentHolder', 'creator'])
            ->where('department_id', $deptId)
            ->latest()->take(7)->get();

        // Recent activity in department
        $recentActivity = FileMovement::with(['file', 'fromUser', 'toUser'])
            ->where(fn($q) => $q->where('from_department', $deptId)
                                ->orWhere('to_department', $deptId))
            ->latest()->take(7)->get();

        // Pending transfers to approve
        $pendingApprovals = TransferRequest::with(['file', 'sender', 'receiver', 'fromDept'])
            ->where('status', 'pending')
            ->where('to_department', $deptId)
            ->latest()->get();

        // Recent users in department
        $recentUsers = User::with('designation')
            ->where('department_id', $deptId)
            ->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'deptFiles', 'deptUsers', 'pendingRequests', 'completedTransfers',
            'recentFiles', 'recentActivity', 'pendingApprovals', 'recentUsers'
        ));
    }
}
