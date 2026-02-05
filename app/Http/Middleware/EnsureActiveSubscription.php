<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has an active subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Allow read-only operations without subscription
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Require subscription for mutations (POST, PUT, DELETE)
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Active subscription required to create or modify resources.',
                'subscription_status' => $user->subscription_status,
                'expires_at' => $user->subscription_expires_at,
            ], 403);
        }

        return $next($request);
    }
}
