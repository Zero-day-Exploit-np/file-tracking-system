<?php

namespace App\Http\Controllers;

use App\Support\NotificationPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function poll()
    {
        $user = Auth::user();
        $latest = $user->notifications()
            ->latest()
            ->limit(15)
            ->get();

        return response()->json([
            'unread_count' => $user->notifications()->whereNull('read_at')->count(),
            'notifications' => $latest
                ->map(fn($notification) => NotificationPresenter::present($notification))
                ->values(),
        ]);
    }

    public function markVisibleAsRead(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'max:15'],
            'ids.*' => ['required', 'uuid'],
        ]);

        $user = Auth::user();

        $user->notifications()
            ->whereIn('id', $data['ids'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => $user->notifications()->whereNull('read_at')->count(),
        ]);
    }
}
