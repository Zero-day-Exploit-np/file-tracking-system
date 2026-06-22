<?php

namespace App\Http\Controllers;

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
     * JSON endpoint polled every 30 s for unread count + latest notifications.
     * Also used to trigger notification sound on new items.
     */
    public function poll()
    {
        $user  = Auth::user();
        $unread = $user->unreadNotifications;

        return response()->json([
            'unread_count' => $unread->count(),
            'notifications' => $unread->take(5)->map(fn($n) => [
                'id'      => $n->id,
                'message' => $n->data['message'] ?? 'New notification',
                'type'    => $n->data['type']    ?? 'info',
                'time'    => $n->created_at->diffForHumans(),
            ]),
        ]);
    }
}
