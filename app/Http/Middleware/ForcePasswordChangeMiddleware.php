<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * If the authenticated user has must_change_password = true,
 * redirect every request to the change-password page except:
 * - the change-password route itself
 * - logout
 * - profile password update
 */
class ForcePasswordChangeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->must_change_password) {
            $allowed = [
                route('profile.password.update', [], false),
                route('logout', [], false),
                route('profile.edit', [], false),
            ];

            $currentPath = '/' . ltrim($request->path(), '/');

            if (!in_array($currentPath, $allowed, true)) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}
