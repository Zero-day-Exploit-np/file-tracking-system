<?php

namespace App\Http\Controllers;

use App\Models\FileMovement;
use App\Models\FileRecord;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;

        // Cached KPIs
        $stats = $this->dashboard->userStats($userId);

        // Recent data (always fresh, eager loaded)
        $myFiles = FileRecord::with(['department', 'currentHolder'])
            ->where(fn($q) => $q->where('created_by', $userId)->orWhere('current_user_id', $userId))
            ->latest()->take(10)->get();

        $recentActivity = FileMovement::with(['file', 'fromUser', 'toUser', 'fromDept', 'toDept'])
            ->where(fn($q) => $q->where('from_user', $userId)->orWhere('to_user', $userId))
            ->latest()->take(8)->get();

        $unreadNotifications = $user->unreadNotifications->take(5);

        return view('user.dashboard', [
            'myFiles'              => $myFiles,
            'totalMyFiles'         => $stats['total_my_files'],
            'sentFiles'            => $stats['sent_files'],
            'receivedFiles'        => $stats['received_files'],
            'pendingTransfers'     => $stats['pending_transfers'],
            'recentActivity'       => $recentActivity,
            'unreadNotifications'  => $unreadNotifications,
        ]);
    }
}
