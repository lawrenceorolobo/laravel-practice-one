<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'subscription_paid' => \App\Http\Middleware\EnsureSubscriptionPaid::class,
            'ensure_admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        
        // Trust ngrok proxy for HTTPS
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sanitize database errors - NEVER expose SQL to users
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // Log the actual error for debugging
                logger()->error('Database error', [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql() ?? 'N/A',
                ]);

                // Check for connection errors
                if (str_contains($e->getMessage(), 'Connection refused') ||
                    str_contains($e->getMessage(), 'No connection could be made')) {
                    return response()->json([
                        'message' => 'Service temporarily unavailable. Please try again later.',
                    ], 503);
                }

                // Check for missing table errors
                if (str_contains($e->getMessage(), 'Base table or view not found') ||
                    str_contains($e->getMessage(), "doesn't exist")) {
                    return response()->json([
                        'message' => 'Service configuration error. Please contact support.',
                    ], 503);
                }

                // Generic database error
                return response()->json([
                    'message' => 'An unexpected error occurred. Please try again.',
                ], 500);
            }

            // For web requests, show a friendly error page
            return response()->view('errors.500', [], 500);
        });

        // Handle PDO exceptions (connection issues)
        $exceptions->render(function (\PDOException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                logger()->error('PDO error', ['message' => $e->getMessage()]);

                return response()->json([
                    'message' => 'Service temporarily unavailable. Please try again later.',
                ], 503);
            }

            return response()->view('errors.500', [], 500);
        });

        // Handle 404s gracefully
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });
    })->create();
