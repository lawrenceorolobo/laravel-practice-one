<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Check if email already exists
        $existing = User::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => ['An account with this email already exists. Try signing in instead.'],
                ]);
            }

            // Unverified — update their details and resend OTP
            $existing->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'password' => $validated['password'],
                'company_name' => $validated['company_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            $existing->tokens()->delete();
            $this->sendOtpToUser($existing);
            $token = $existing->createToken('auth-token')->plainTextToken;
            $user = $existing;
        } else {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'company_name' => $validated['company_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            $this->sendOtpToUser($user);
            $token = $user->createToken('auth-token')->plainTextToken;
        }

        return response()->json([
            'message' => 'Account created! Check your email for a verification code.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'email_verified' => false,
                'subscription_status' => $user->subscription_status,
                'has_active_subscription' => $user->hasActiveSubscription(),
            ],
            'token' => $token,
            'requires_otp' => true,
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
                'email' => ["Too many attempts. Try again in {$seconds} seconds."],
            ]);
        }

        // Generic error to prevent user enumeration
        $genericError = 'Wrong email or password. Give it another shot.';

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => [$genericError],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This account has been deactivated. Reach out to support if you need help.'],
            ]);
        }

        RateLimiter::clear($key);

        // Revoke old tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Welcome back!',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'subscription_status' => $user->subscription_status,
                'subscription_expires_at' => $user->subscription_expires_at,
                'has_active_subscription' => $user->hasActiveSubscription(),
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
            'message' => 'You\'ve been signed out.',
        ]);
    }

    /**
     * Get current authenticated user profile
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company_name' => $user->company_name,
                'email_verified' => $user->hasVerifiedEmail(),
                'subscription_status' => $user->subscription_status,
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
            $token = app('auth.password.broker')->createToken($user);
            dispatch(function () use ($user, $token) {
                try {
                    $user->sendPasswordResetNotification($token);
                } catch (\Exception $e) {
                    logger()->error('Password reset email failed', ['error' => $e->getMessage()]);
                }
            })->afterResponse();
        }

        return response()->json([
            'message' => 'If we find an account with that email, we\'ll send a reset link your way.',
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
                'email' => ['That reset link doesn\'t look right. Try requesting a new one.'],
            ]);
        }

        $broker = app('auth.password.broker');
        if (!$broker->tokenExists($user, $request->token)) {
            throw ValidationException::withMessages([
                'email' => ['This reset link has expired. Request a fresh one and try again.'],
            ]);
        }

        $user->update([
            'password' => $request->password,
        ]);

        $broker->deleteToken($user);
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password updated! You can sign in with your new password now.',
        ]);
    }

    /**
     * Send OTP for email verification
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email is already verified — you\'re all set.',
            ]);
        }

        // Rate limit: 3 OTPs per 10 minutes
        $key = 'otp-send:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => ["Hold on — wait {$seconds} seconds before requesting another code."],
            ]);
        }

        $this->sendOtpToUser($user);
        RateLimiter::hit($key, 600);

        return response()->json([
            'message' => 'New code sent! Check your inbox.',
        ]);
    }

    /**
     * Verify OTP and mark email as verified
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email is already verified — you\'re all set.',
            ]);
        }

        // Rate limit verify attempts
        $attemptKey = 'otp-verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($attemptKey, 5)) {
            throw ValidationException::withMessages([
                'otp' => ['Too many wrong tries. Request a new code and try again.'],
            ]);
        }

        $cacheKey = 'email_otp:' . $user->id;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || !hash_equals($storedOtp, $validated['otp'])) {
            RateLimiter::hit($attemptKey, 600);
            throw ValidationException::withMessages([
                'otp' => ['That code didn\'t work. Double-check it or request a new one.'],
            ]);
        }

        // Success - verify email
        $user->markEmailAsVerified();
        Cache::forget($cacheKey);
        RateLimiter::clear($attemptKey);

        return response()->json([
            'message' => 'Email verified! You\'re good to go.',
            'email_verified' => true,
        ]);
    }

    /**
     * Internal: Generate and send OTP to user
     */
    protected function sendOtpToUser(User $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes
        Cache::put('email_otp:' . $user->id, $otp, now()->addMinutes(10));

        // Dispatch to queue — truly non-blocking
        SendEmailJob::dispatch($user->email, new OtpVerificationMail($user, $otp));
    }
}
