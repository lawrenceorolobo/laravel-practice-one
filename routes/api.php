<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InviteeController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TestController;
use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Middleware\RateLimitByTier;
use App\Http\Middleware\SanitizeInput;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Global middleware for all API routes
Route::middleware([SanitizeInput::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes (Rate Limited)
    |--------------------------------------------------------------------------
    */
    Route::middleware([RateLimitByTier::class . ':auth'])->group(function () {
        // Business Admin Auth
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

        // Super Admin Auth
        Route::post('/admin/auth/login', [AdminAuthController::class, 'login']);
    });

    // Test-Taker Routes (Public, Token-based)
    Route::prefix('test')->middleware([RateLimitByTier::class . ':test'])->group(function () {
        Route::get('/validate/{token}', [TestController::class, 'validateToken']);
        Route::post('/start/{token}', [TestController::class, 'startSession']);
        Route::get('/questions/{token}', [TestController::class, 'getQuestions']);
        Route::post('/answer/{token}', [TestController::class, 'submitAnswer']);
        Route::post('/submit/{token}', [TestController::class, 'submit']);
        Route::post('/proctoring/{token}', [TestController::class, 'logProctoringEvent']);

        // Public access link (anyone with code can join)
        Route::get('/access/{accessCode}', [TestController::class, 'validateAccessCode']);
        Route::post('/join/{accessCode}', [TestController::class, 'joinByAccessCode']);
    });

    // Subscription Plans (Public)
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);

    // Paystack Webhook (No auth, signature verified in controller)
    Route::post('/webhooks/paystack', [SubscriptionController::class, 'webhook']);

    // Flutterwave Webhook (No auth, signature verified in controller)
    Route::post('/webhooks/flutterwave', [\App\Http\Controllers\Api\PaymentController::class, 'webhook']);

    /*
    |--------------------------------------------------------------------------
    | Authenticated Business Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', RateLimitByTier::class . ':api'])->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/profile', [AuthController::class, 'me']);
        Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);

        // Dashboard Stats
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/activity', [DashboardController::class, 'activity']);
        Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);

        // Subscription (requires auth)
        Route::get('/subscription/status', [SubscriptionController::class, 'status']);
        Route::post('/subscription/initialize', [SubscriptionController::class, 'initialize'])
            ->middleware(IdempotencyMiddleware::class);
        Route::post('/subscription/verify', [SubscriptionController::class, 'verify']);

        // Flutterwave Payments (requires auth, no subscription needed)
        Route::post('/payments/initialize', [\App\Http\Controllers\Api\PaymentController::class, 'initialize']);

        /*
        |--------------------------------------------------------------------------
        | Subscription Required Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware([EnsureActiveSubscription::class])->group(function () {
            // Assessments
            Route::apiResource('assessments', AssessmentController::class);
            Route::post('/assessments/{assessment}/publish', [AssessmentController::class, 'publish']);
            Route::get('/assessments/{assessment}/results', [AssessmentController::class, 'results']);
            Route::get('/assessments/{assessment}/analytics', [AssessmentController::class, 'analytics']);

            // Questions
            Route::post('/assessments/{assessment}/questions', [QuestionController::class, 'store']);
            Route::put('/assessments/{assessment}/questions/{question}', [QuestionController::class, 'update']);
            Route::delete('/assessments/{assessment}/questions/{question}', [QuestionController::class, 'destroy']);
            Route::post('/assessments/{assessment}/questions/reorder', [QuestionController::class, 'reorder']);
            Route::post('/assessments/{assessment}/questions/import', [QuestionController::class, 'importCSV']);

            // Invitees
            Route::get('/assessments/{assessment}/invitees', [InviteeController::class, 'index']);
            Route::post('/assessments/{assessment}/invitees', [InviteeController::class, 'store']);
            Route::put('/assessments/{assessment}/invitees/{invitee}', [InviteeController::class, 'update']);
            Route::delete('/assessments/{assessment}/invitees/{invitee}', [InviteeController::class, 'destroy']);
            Route::post('/assessments/{assessment}/invitees/send', [InviteeController::class, 'sendInvites']);
            Route::post('/assessments/{assessment}/invitees/{invitee}/resend', [InviteeController::class, 'resend']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['auth:sanctum', 'ensure_admin'])->group(function () {
        Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
        Route::get('/auth/me', [AdminAuthController::class, 'me']);

        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/revenue', [AdminController::class, 'revenue']);
        Route::get('/settings', [AdminController::class, 'settings']);
        Route::put('/settings', [AdminController::class, 'updateSettings']);

        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/{id}', [AdminController::class, 'showUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUser']);

        // Reports
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::get('/reports/export-pdf', [AdminController::class, 'exportReportsPdf']);

        // Subscription Plans CRUD
        Route::get('/subscription-plans', [AdminController::class, 'subscriptionPlans']);
        Route::post('/subscription-plans', [AdminController::class, 'createPlan']);
        Route::put('/subscription-plans/{id}', [AdminController::class, 'updatePlan']);
        Route::delete('/subscription-plans/{id}', [AdminController::class, 'deletePlan']);

        // Assessment Management
        Route::get('/assessments', [AdminController::class, 'assessments']);
        Route::get('/assessments/{id}', [AdminController::class, 'showAssessment']);
        Route::delete('/assessments/{id}', [AdminController::class, 'deleteAssessment']);
    });
});
