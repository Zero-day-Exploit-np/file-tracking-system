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

    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications have been marked as read.');
    }
}
