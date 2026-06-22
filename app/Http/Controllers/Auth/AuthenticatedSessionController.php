<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Log successful login
        AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => 'login',
            'auditable_type' => 'App\Models\User',
            'auditable_id'   => Auth::id(),
            'description'    => 'User logged in',
            'metadata'       => [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $ip     = $request->ip();

        // Log logout before session is destroyed
        if ($userId) {
            AuditLog::create([
                'user_id'        => $userId,
                'action'         => 'logout',
                'auditable_type' => 'App\Models\User',
                'auditable_id'   => $userId,
                'description'    => 'User logged out',
                'metadata'       => ['ip' => $ip],
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
