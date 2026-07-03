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
     * Polling endpoint — returns unread notification count.
     * Polled every 15 s by the frontend layout.
     */
    public function poll()
    {
        $user   = Auth::user();
        $unread = $user->unreadNotifications;

        return response()->json([
            'unread_count'   => $unread->count(),
            'notifications'  => $unread->take(5)->map(fn($n) => [
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
