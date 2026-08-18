<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Audit trail. Logs grants AND denials.
 *
 * NEVER pass secrets into $meta — no passwords, OTP codes, secret passes,
 * or file contents. Identifiers and reasons only.
 */
class AccessLogger
{
    public static function success($user, string $action, Request $request, array $meta = []): void
    {
        self::write($user, $action, 'success', $request, $meta);
    }

    public static function denied($user, string $action, Request $request, array $meta = []): void
    {
        self::write($user, $action, 'denied', $request, $meta);
    }

    protected static function write($user, string $action, string $result, Request $request, array $meta): void
    {
        DB::table('access_logs')->insert([
            'user_id'       => $user?->id,
            'action'        => $action,
            'resource_type' => $meta['resource_type'] ?? null,
            'resource_id'   => $meta['resource_id'] ?? null,
            'result'        => $result,
            'ip'            => $request->ip(),
            'user_agent'    => substr((string) $request->userAgent(), 0, 500),
            'meta'          => json_encode($meta),
            'created_at'    => now(),
        ]);
    }
}
