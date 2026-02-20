<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Ensure the authenticated user is an Admin (not a regular User).
     * This middleware MUST be applied to all admin routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Must be authenticated
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Must be an Admin model instance (not User)
        if (!$user instanceof Admin) {
            logger()->warning('Non-admin attempted to access admin route', [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);

            return response()->json([
                'message' => 'Access denied.',
            ], 403);
        }

        // Verify token has admin ability
        $token = $user->currentAccessToken();
        if (!$token || !$token->can('admin:full')) {
            logger()->warning('Admin token missing required ability', [
                'admin_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Insufficient privileges.',
            ], 403);
        }

        // Ensure admin account is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Admin account disabled.',
            ], 403);
        }

        return $next($request);
    }
}
