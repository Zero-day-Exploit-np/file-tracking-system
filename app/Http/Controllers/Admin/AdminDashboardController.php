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
        $user = Auth::user();
        $isSuper = $user->role === 'super_admin';
        $deptId  = $user->department_id;

        // KPI counts (scoped by department for admin)
        $totalUsers        = $isSuper ? User::count() : User::where('department_id', $deptId)->count();
        $totalDepartments  = $isSuper ? Department::count() : 1;
        $totalDesignations = $isSuper ? Designation::count() : Designation::where('department_id', $deptId)->count();

        $filesQuery  = FileRecord::query();
        if (!$isSuper) $filesQuery->where('department_id', $deptId);
        $totalFiles = $filesQuery->count();

        $pendingQuery = TransferRequest::where('status', 'pending');
        if (!$isSuper) $pendingQuery->where('to_department', $deptId);
        $pendingTransfers = $pendingQuery->count();

        $publicSubmissions = PublicFile::count();

        // Recent transfers (with eager load)
        $recentTransfers = FileTransfer::with(['sender', 'receiver', 'file'])
            ->latest()->take(7)->get();

        // Recent files
        $recentFiles = FileRecord::with(['department', 'currentHolder'])
            ->when(!$isSuper, fn($q) => $q->where('department_id', $deptId))
            ->latest()->take(7)->get();

        // Recent audit / movements
        $recentAudit = FileMovement::with(['file', 'fromUser', 'toUser'])
            ->when(!$isSuper, function ($q) use ($deptId) {
                $q->where(fn($q2) => $q2
                    ->where('from_department', $deptId)
                    ->orWhere('to_department', $deptId));
            })
            ->latest()->take(7)->get();

        // Recent users
        $recentUsers = User::with(['designation', 'department'])
            ->when(!$isSuper, fn($q) => $q->where('department_id', $deptId))
            ->latest()->take(5)->get();

        // Timeline action stats
        $timelineStats = [
            'created'     => FileMovement::where('action', 'created')->count(),
            'requested'   => FileMovement::where('action', 'requested')->count(),
            'approved'    => FileMovement::where('action', 'approved')->count(),
            'rejected'    => FileMovement::where('action', 'rejected')->count(),
            'transferred' => FileMovement::where('action', 'transferred')->count(),
        ];

        // Department file counts (super admin only)
        $departmentFileCounts = $isSuper
            ? Department::withCount('files')->orderByDesc('files_count')->take(8)->get()
            : collect();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDepartments', 'totalDesignations', 'totalFiles',
            'pendingTransfers', 'publicSubmissions',
            'recentTransfers', 'recentFiles', 'recentAudit', 'recentUsers',
            'timelineStats', 'departmentFileCounts'
        ));
    }
}
