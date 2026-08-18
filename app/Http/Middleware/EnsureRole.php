<?php

namespace App\Http\Middleware;

use App\Services\AccessLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server-side role gate. Usage:  ->middleware('role:founder,selected_oc')
 *
 * Hiding a menu item is NOT access control. Every protected route
 * carries this middleware, and every denial is logged.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || $user->status !== 'active') {
            AccessLogger::denied($user, 'role_gate', $request, ['required' => $roles, 'why' => 'inactive']);
            abort(403);
        }

        if (! in_array($user->role->slug, $roles, true)) {
            AccessLogger::denied($user, 'role_gate', $request, [
                'required' => $roles,
                'held' => $user->role->slug,
                'path' => $request->path(),
            ]);
            abort(403);
        }

        return $next($request);
    }
}
