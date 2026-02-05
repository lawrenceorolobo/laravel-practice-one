<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Admin login - most secure endpoint
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Strict rate limiting for admin
        $key = 'admin-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Log suspicious activity
            logger()->warning('Admin login rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'email_attempted' => $validated['email'],
            ]);

            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $genericError = 'The provided credentials are incorrect.';

        $admin = Admin::where('email', $validated['email'])->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            RateLimiter::hit($key, 300); // 5 minute lockout
            
            logger()->warning('Failed admin login attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'email_attempted' => $validated['email'],
            ]);

            throw ValidationException::withMessages([
                'email' => [$genericError],
            ]);
        }

        if (!$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This admin account has been disabled.'],
            ]);
        }

        RateLimiter::clear($key);

        // Update last login
        $admin->update(['last_login_at' => now()]);

        // Revoke old tokens
        $admin->tokens()->delete();

        $token = $admin->createToken('admin-token', ['admin:full'])->plainTextToken;

        logger()->info('Admin login successful', [
            'admin_id' => $admin->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Admin login successful.',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Admin logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Admin logged out successfully.',
        ]);
    }

    /**
     * Get current admin info
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'last_login_at' => $admin->last_login_at,
            ],
        ]);
    }
}
