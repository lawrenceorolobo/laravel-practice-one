<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public landing pages
Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');

// Auth pages
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
Route::view('/verify-email', 'auth.verify-email')->name('verify-email');

// Payment pages
Route::view('/select-plan', 'payment.select-plan')->name('select-plan');
Route::get('/payment/callback', [\App\Http\Controllers\Api\PaymentController::class, 'callback'])->name('payment.callback');
Route::view('/payment/verifying', 'payment.verifying')->name('payment.verifying');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth:sanctum')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }
    
    if ($user->hasVerifiedEmail()) {
        return redirect('/dashboard?verified=already');
    }
    
    $user->markEmailAsVerified();
    
    return redirect('/dashboard?verified=1');
})->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Business Admin Dashboard (protected by frontend auth check)
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/assessments', 'user.assessments')->name('assessments');
Route::view('/assessments/create', 'user.assessment-create')->name('assessments.create');
Route::view('/assessments/{id}', 'user.assessment-view')->name('assessments.view');
Route::view('/assessments/{id}/edit', 'user.assessment-edit')->name('assessments.edit');
Route::view('/candidates', 'user.candidates')->name('candidates');
Route::view('/analytics', 'user.analytics')->name('analytics');
Route::view('/settings', 'user.settings')->name('settings');

// Test-taker interface
Route::get('/test/{token}', function ($token) {
    return view('test.take', ['token' => $token]);
})->name('test.take');

// Public assessment link (anyone with access code can join)
Route::get('/join/{accessCode}', function ($accessCode) {
    return view('test.join', ['accessCode' => $accessCode]);
})->name('test.join');

// Admin pages
Route::view('/admin/login', 'auth.admin-login')->name('admin.login');
Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
Route::view('/admin/users', 'admin.users')->name('admin.users');
Route::view('/admin/subscription-plans', 'admin.subscription-plans')->name('admin.subscription-plans');
Route::view('/admin/assessments', 'admin.assessments')->name('admin.assessments');
Route::view('/admin/reports', 'admin.reports')->name('admin.reports');
Route::view('/admin/settings', 'admin.settings')->name('admin.settings');