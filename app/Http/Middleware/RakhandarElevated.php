<?php

namespace App\Http\Middleware;

use App\Services\AccessLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fourth and final gate for Ultimate Rakhandar.
 *
 * Re-checks ALL conditions on every request — the elevated flag alone is
 * never sufficient. Stack it after auth and role:
 *
 *   Route::middleware(['auth', 'role:founder,selected_oc', 'rakhandar.elevated',
 *                      'throttle:30,1'])
 *
 * Applies to page loads AND file downloads.
 */
class RakhandarElevated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Condition 1 + 2 — re-verified, not assumed from the earlier middleware
        if (! $user || $user->status !== 'active'
            || ! in_array($user->role->slug, ['founder', 'selected_oc'], true)) {
            AccessLogger::denied($user, 'rakhandar_access', $request, ['why' => 'role']);
            abort(403);
        }

        // Condition 3 — a shared pass must actually be set for this account
        if (! $user->rakhandar_pass_hash) {
            AccessLogger::denied($user, 'rakhandar_access', $request, ['why' => 'no_pass_set']);
            return redirect()->route('rakhandar.unlock')
                ->with('notice', 'Set your Rakhandar access pass to continue.');
        }

        // Conditions 3 + 4 completed -> elevated session, 15 minutes
        $until = $request->session()->get('rakhandar_elevated_until');
        $sessionBound = $request->session()->get('rakhandar_elevated_sid');

        $valid = $until
            && now()->timestamp < $until
            && $sessionBound === $request->session()->getId();   // not portable to another session

        if (! $valid) {
            $request->session()->forget(['rakhandar_elevated_until', 'rakhandar_elevated_sid']);
            AccessLogger::denied($user, 'rakhandar_access', $request, ['why' => 'not_elevated']);

            return redirect()->route('rakhandar.unlock')
                ->with('intended', $request->fullUrl());
        }

        AccessLogger::success($user, 'rakhandar_access', $request, ['path' => $request->path()]);

        return $next($request);
    }
}
