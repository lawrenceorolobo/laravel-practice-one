<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new business admin
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        // TODO: Implement full email verification flow
        // For now, auto-verify users to avoid route errors
        $user->markEmailAsVerified();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'email_verified' => true,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login existing user
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        // Generic error to prevent user enumeration
        $genericError = 'The provided credentials are incorrect.';

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => [$genericError],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been disabled. Please contact support.'],
            ]);
        }

        RateLimiter::clear($key);

        // Revoke old tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'subscription_status' => $user->subscription_status,
                'subscription_expires_at' => $user->subscription_expires_at,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Logout current user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $user->company_name,
                'email_verified' => $user->hasVerifiedEmail(),
                'subscription_status' => $user->subscription_status,
                'subscription_expires_at' => $user->subscription_expires_at,
                'has_active_subscription' => $user->hasActiveSubscription(),
            ],
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Always return success to prevent email enumeration
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->sendPasswordResetNotification(
                app('auth.password.broker')->createToken($user)
            );
        }

        return response()->json([
            'message' => 'If an account exists with that email, you will receive a password reset link.',
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Invalid reset token.'],
            ]);
        }

        $broker = app('auth.password.broker');
        if (!$broker->tokenExists($user, $request->token)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid or expired reset token.'],
            ]);
        }

        $user->update([
            'password' => $request->password,
        ]);

        $broker->deleteToken($user);
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
