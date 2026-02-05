<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle idempotent requests using idempotency keys
     * Prevents duplicate operations (e.g., double payments)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to state-changing methods
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');
        
        if (!$idempotencyKey) {
            return $next($request);
        }

        // Validate key format
        if (!preg_match('/^[a-zA-Z0-9\-_]{8,64}$/', $idempotencyKey)) {
            return response()->json([
                'message' => 'Invalid Idempotency-Key format.',
            ], 400);
        }

        $userId = $request->user()?->id ?? 'anonymous';
        $cacheKey = "idempotency:{$userId}:{$idempotencyKey}";
        $requestHash = hash('sha256', json_encode([
            'method' => $request->method(),
            'path' => $request->path(),
            'body' => $request->all(),
        ]));

        // Check for cached response
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            // Verify request hash matches
            if ($cached['request_hash'] !== $requestHash) {
                return response()->json([
                    'message' => 'Idempotency-Key already used for different request.',
                ], 422);
            }

            // Return cached response
            return response()->json(
                $cached['response_body'],
                $cached['response_code']
            )->header('Idempotency-Replay', 'true');
        }

        // Mark key as processing (prevents concurrent duplicates)
        $lockKey = "idempotency_lock:{$cacheKey}";
        if (!Cache::add($lockKey, true, 30)) {
            return response()->json([
                'message' => 'Request already being processed.',
            ], 409);
        }

        try {
            $response = $next($request);

            // Cache successful responses (2xx) for 24 hours
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                Cache::put($cacheKey, [
                    'request_hash' => $requestHash,
                    'response_code' => $response->getStatusCode(),
                    'response_body' => json_decode($response->getContent(), true),
                ], 86400);
            }

            return $response;
        } finally {
            Cache::forget($lockKey);
        }
    }
}
