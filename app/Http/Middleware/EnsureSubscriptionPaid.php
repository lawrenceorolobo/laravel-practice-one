<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionPaid
{
    /**
     * Routes that don't require subscription
     */
    private array $excludedRoutes = [
        'api/payments/*',
        'api/subscription/plans',
        'api/auth/logout',
        'api/auth/me',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check if route is excluded
        foreach ($this->excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Subscription required',
                'requires_payment' => true,
                'redirect' => '/select-plan',
            ], 402);
        }

        return $next($request);
    }
}
