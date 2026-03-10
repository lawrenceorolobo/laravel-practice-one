<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    /**
     * Reject requests from deactivated users.
     * Returns 403 with "account_deactivated" code so frontend can auto-logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->is_active) {
            // Revoke current token
            $user->currentAccessToken()?->delete();

            return response()->json([
                'message' => 'Your account has been deactivated.',
                'code' => 'account_deactivated',
            ], 403);
        }

        return $next($request);
    }
}
