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

class AdminDashboardController extends Controller
{
    public function index()
    {
        $recentTransfers = FileTransfer::with(['sender.designation', 'receiver.designation', 'file'])
            ->latest()
            ->take(5)
            ->get();

        $recentFiles = FileRecord::with(['department', 'currentUser'])
            ->latest()
            ->take(5)
            ->get();

        $recentAudit = FileMovement::with(['file', 'fromUser', 'toUser'])
            ->latest()
            ->take(5)
            ->get();

        $timelineStats = [
            'created' => FileMovement::where('action', 'created')->count(),
            'requested' => FileMovement::where('action', 'requested')->count(),
            'approved' => FileMovement::where('action', 'approved')->count(),
            'rejected' => FileMovement::where('action', 'rejected')->count(),
            'transferred' => FileMovement::where('action', 'transferred')->count(),
            'delivered' => FileMovement::where('action', 'delivered')->count(),
        ];

        $departmentFileCounts = Department::withCount('files')->get();

        return view('admin.dashboard', [
            'users' => User::count(),
            'departments' => Department::count(),
            'designations' => Designation::count(),
            'files' => FileRecord::count(),
            'pendingTransfers' => TransferRequest::query()->where('status', 'pending')->count(),
            'publicSubmissions' => PublicFile::count(),
            'recentTransfers' => $recentTransfers,
            'recentFiles' => $recentFiles,
            'recentAudit' => $recentAudit,
            'timelineStats' => $timelineStats,
            'departmentFileCounts' => $departmentFileCounts,
            'recentUsers' => User::query()->with(['designation', 'department'])->latest()->take(5)->get(),
        ]);
    }
}
