<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitByTier
{
    /**
     * Rate limit tiers for different endpoint types
     */
    protected array $tiers = [
        'auth' => ['limit' => 5, 'window' => 60],
        'api' => ['limit' => 100, 'window' => 60],
        'test' => ['limit' => 1, 'window' => 5],
        'webhook' => ['limit' => 50, 'window' => 60],
    ];

    public function handle(Request $request, Closure $next, string $tier = 'api'): Response
    {
        $config = $this->tiers[$tier] ?? $this->tiers['api'];
        $key = $this->resolveKey($request, $tier);
        
        $hits = Cache::get($key, 0);
        
        if ($hits >= $config['limit']) {
            return response()->json([
                'message' => 'Too many requests.',
                'retry_after' => $config['window'],
            ], 429);
        }
        
        Cache::put($key, $hits + 1, $config['window']);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $config['limit']);
        $response->headers->set('X-RateLimit-Remaining', max(0, $config['limit'] - $hits - 1));
        
        return $response;
    }

    protected function resolveKey(Request $request, string $tier): string
    {
        $identifier = $request->user()?->id ?? $request->ip();
        return "rate_limit:{$tier}:{$identifier}";
    }
}
