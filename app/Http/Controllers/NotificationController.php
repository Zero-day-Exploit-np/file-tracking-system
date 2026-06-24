<?php

namespace App\Http\Controllers;

use App\Models\TransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Unified polling endpoint — returns unread notifications AND
     * pending transfer counts. Polled every 15 s by the frontend.
     */
    public function poll()
    {
        $user   = Auth::user();
        $unread = $user->unreadNotifications;

        // Pending transfer count for admin (from their dept)
        $pendingTransfers = 0;
        if ($user->role === 'admin' && $user->department_id) {
            $pendingTransfers = TransferRequest::where('status', 'pending')
                ->where('from_department', $user->department_id)
                ->count();
        }

        return response()->json([
            'unread_count'      => $unread->count(),
            'pending_transfers' => $pendingTransfers,
            'notifications'     => $unread->take(5)->map(fn($n) => [
                'id'      => $n->id,
                'message' => $n->data['message'] ?? 'New notification',
                'type'    => $n->data['type']    ?? 'info',
                'time'    => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
