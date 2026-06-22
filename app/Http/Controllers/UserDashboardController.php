<?php

namespace App\Http\Controllers;

use App\Models\FileRecord;
use App\Models\FileMovement;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;
        $deptId = $user->department_id;

        // My Files — files I created or currently hold
        $myFiles = FileRecord::with(['department', 'currentHolder'])
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('current_user_id', $userId);
            })
            ->latest()
            ->take(10)
            ->get();

        // KPI counts
        $totalMyFiles = FileRecord::where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhere('current_user_id', $userId);
        })->count();

        $sentFiles = FileMovement::where('from_user', $userId)
            ->where('action', 'transferred')
            ->count();

        $receivedFiles = FileMovement::where('to_user', $userId)
            ->whereIn('action', ['transferred', 'approved'])
            ->count();

        $pendingTransfers = TransferRequest::where('requested_by', $userId)
            ->where('status', 'pending')
            ->count();

        // Recent activity — movements I was involved in
        $recentActivity = FileMovement::with(['file', 'fromUser', 'toUser', 'fromDept', 'toDept'])
            ->where(function ($q) use ($userId) {
                $q->where('from_user', $userId)
                  ->orWhere('to_user', $userId);
            })
            ->latest()
            ->take(8)
            ->get();

        // Unread notifications
        $unreadNotifications = $user->unreadNotifications->take(5);

        return view('user.dashboard', compact(
            'myFiles',
            'totalMyFiles',
            'sentFiles',
            'receivedFiles',
            'pendingTransfers',
            'recentActivity',
            'unreadNotifications'
        ));
    }
}
