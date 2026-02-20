<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Assessment;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * List all business admins
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query()
            ->withCount(['assessments', 'payments' => fn ($q) => $q->where('status', 'success')])
            ->withSum(['payments' => fn ($q) => $q->where('status', 'success')], 'amount');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filter by subscription status
        if ($status = $request->input('subscription_status')) {
            $query->where('subscription_status', $status);
        }

        // Filter by active/disabled
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($users);
    }

    /**
     * Get single user details
     */
    public function showUser(string $id): JsonResponse
    {
        $user = User::withTrashed()
            ->withCount('assessments')
            ->with(['payments' => fn ($q) => $q->latest()->limit(10)])
            ->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Toggle user active status (enable/disable)
     */
    public function toggleUser(Request $request, string $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        logger()->info('Admin toggled user status', [
            'admin_id' => $request->user()->id,
            'user_id' => $id,
            'new_status' => $user->is_active,
        ]);

        return response()->json([
            'message' => $user->is_active ? 'User enabled.' : 'User disabled.',
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * Update user (admin can edit any user)
     */
    public function updateUser(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'subscription_status' => ['sometimes', 'in:none,active,expired,cancelled'],
            'subscription_expires_at' => ['nullable', 'date'],
        ]);

        $user->update($validated);

        logger()->info('Admin updated user', [
            'admin_id' => $request->user()->id,
            'user_id' => $id,
            'changes' => array_keys($validated),
        ]);

        return response()->json([
            'message' => 'User updated.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Revenue analytics
     */
    public function revenue(Request $request): JsonResponse
    {
        // Total revenue
        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        // Monthly revenue (last 12 months)
        $monthlyRevenue = Payment::where('status', 'success')
            ->where('paid_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as revenue, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue by billing cycle
        $byBillingCycle = Payment::where('status', 'success')
            ->selectRaw('billing_cycle, SUM(amount) as revenue, COUNT(*) as count')
            ->groupBy('billing_cycle')
            ->get();

        // User stats
        $totalUsers = User::count();
        $activeSubscribers = User::where('subscription_status', 'active')->count();
        $expiredSubscribers = User::where('subscription_status', 'expired')->count();
        $neverSubscribed = User::where('subscription_status', 'none')->count();

        // Recent payments
        $recentPayments = Payment::with('user:id,first_name,last_name,email')
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->limit(20)
            ->get(['id', 'user_id', 'amount', 'billing_cycle', 'paid_at']);

        return response()->json([
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_users' => $totalUsers,
                'active_subscribers' => $activeSubscribers,
                'expired_subscribers' => $expiredSubscribers,
                'never_subscribed' => $neverSubscribed,
                'conversion_rate' => $totalUsers > 0 
                    ? round(($activeSubscribers + $expiredSubscribers) / $totalUsers * 100, 1) 
                    : 0,
            ],
            'charts' => [
                'monthly_revenue' => $monthlyRevenue,
                'by_billing_cycle' => $byBillingCycle,
                'user_distribution' => [
                    ['label' => 'Active', 'value' => $activeSubscribers],
                    ['label' => 'Expired', 'value' => $expiredSubscribers],
                    ['label' => 'Never Subscribed', 'value' => $neverSubscribed],
                ],
            ],
            'recent_payments' => $recentPayments,
        ]);
    }

    /**
     * Get platform settings
     */
    public function settings(): JsonResponse
    {
        $settings = Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->typedValue]);

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update platform settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['required'],
            'settings.*.type' => ['required', 'in:string,int,float,bool,json'],
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::setValue(
                $setting['key'],
                $setting['value'],
                $setting['type']
            );
        }

        logger()->info('Admin updated settings', [
            'admin_id' => $request->user()->id,
            'keys' => collect($validated['settings'])->pluck('key')->toArray(),
        ]);

        return response()->json([
            'message' => 'Settings updated.',
        ]);
    }

    /**
     * Platform overview dashboard
     */
    public function dashboard(): JsonResponse
    {
        $lastMonth = now()->subMonth();

        // Single query per table instead of multiple
        $users = DB::selectOne("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_this_month,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN subscription_status = 'active' THEN 1 ELSE 0 END) as active_subscriptions
            FROM users", [$lastMonth]);

        $assessments = DB::selectOne("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month
            FROM assessments", [$lastMonth]);

        $revenue = DB::selectOne("SELECT 
            COALESCE(SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END), 0) as total,
            COALESCE(SUM(CASE WHEN status = 'success' AND paid_at >= ? THEN amount ELSE 0 END), 0) as this_month
            FROM payments", [$lastMonth]);

        return response()->json([
            'users' => [
                'total' => (int) $users->total,
                'new_this_month' => (int) $users->new_this_month,
                'active' => (int) $users->active,
                'active_subscriptions' => (int) $users->active_subscriptions,
            ],
            'assessments' => [
                'total' => (int) $assessments->total,
                'active' => (int) $assessments->active,
                'this_month' => (int) $assessments->this_month,
            ],
            'revenue' => [
                'total' => (float) $revenue->total,
                'this_month' => (float) $revenue->this_month,
            ],
        ]);
    }

    /**
     * Comprehensive reports with date filtering
     */
    public function reports(Request $request): JsonResponse
    {
        try {
            $fromDate = $request->input('from') ? now()->parse($request->input('from'))->startOfDay() : now()->subMonth()->startOfDay();
            $toDate = $request->input('to') ? now()->parse($request->input('to'))->endOfDay() : now()->endOfDay();
            $periodDays = max(1, $fromDate->diffInDays($toDate));
            $prevFromDate = $fromDate->copy()->subDays($periodDays);
            $prevToDate = $fromDate->copy()->subSecond();

            // Current period stats
            $currentRevenue = Payment::where('status', 'success')
                ->whereBetween('paid_at', [$fromDate, $toDate])
                ->sum('amount') ?? 0;
            $currentNewUsers = User::whereBetween('created_at', [$fromDate, $toDate])->count();
            $currentAssessments = Assessment::whereBetween('created_at', [$fromDate, $toDate])->count();
            
            // Count completed tests using updated_at as proxy for completion time
            $currentTestsCompleted = DB::table('invitees')
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$fromDate, $toDate])
                ->count();

            // Previous period for comparison
            $prevRevenue = Payment::where('status', 'success')
                ->whereBetween('paid_at', [$prevFromDate, $prevToDate])
                ->sum('amount') ?? 0;
            $prevNewUsers = User::whereBetween('created_at', [$prevFromDate, $prevToDate])->count();
            $prevAssessments = Assessment::whereBetween('created_at', [$prevFromDate, $prevToDate])->count();
            $prevTests = DB::table('invitees')
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$prevFromDate, $prevToDate])
                ->count();

            // Calculate percentage changes
            $calcChange = fn($curr, $prev) => $prev > 0 ? round((($curr - $prev) / $prev) * 100, 1) : ($curr > 0 ? 100 : 0);

            // Monthly revenue trend (for chart) - last 12 months
            $revenueTrend = [];
            try {
                $revenueTrend = Payment::where('status', 'success')
                    ->where('paid_at', '>=', now()->subMonths(12))
                    ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as revenue")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->toArray();
            } catch (\Exception $e) {
                // Empty if query fails
            }

            // Fill missing months with zero values
            $filledRevenue = [];
            for ($i = 11; $i >= 0; $i--) {
                $monthKey = now()->subMonths($i)->format('Y-m');
                $found = collect($revenueTrend)->firstWhere('month', $monthKey);
                $filledRevenue[] = [
                    'month' => $monthKey,
                    'revenue' => $found['revenue'] ?? 0
                ];
            }

            // Monthly user growth (for chart)
            $userGrowth = [];
            try {
                $userGrowth = User::where('created_at', '>=', now()->subMonths(12))
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as users")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->toArray();
            } catch (\Exception $e) {
                // Empty if query fails
            }

            // Fill missing months with zero values
            $filledUsers = [];
            for ($i = 11; $i >= 0; $i--) {
                $monthKey = now()->subMonths($i)->format('Y-m');
                $found = collect($userGrowth)->firstWhere('month', $monthKey);
                $filledUsers[] = [
                    'month' => $monthKey,
                    'users' => $found['users'] ?? 0
                ];
            }

            // Recent transactions
            $recentTransactions = Payment::with('user:id,first_name,last_name,email')
                ->whereBetween('paid_at', [$fromDate, $toDate])
                ->orderByDesc('paid_at')
                ->limit(20)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'reference' => $p->paystack_reference ?? 'TXN-' . str_pad(substr($p->id, -6), 6, '0', STR_PAD_LEFT),
                    'user_name' => $p->user ? "{$p->user->first_name} {$p->user->last_name}" : 'Unknown',
                    'user_email' => $p->user?->email,
                    'plan' => ucfirst($p->billing_cycle ?? 'monthly'),
                    'amount' => (float) $p->amount,
                    'status' => $p->status,
                    'date' => $p->paid_at?->format('M d, Y'),
                ]);

            return response()->json([
                'period' => [
                    'from' => $fromDate->toDateString(),
                    'to' => $toDate->toDateString(),
                ],
                'stats' => [
                    'total_revenue' => (float) $currentRevenue,
                    'revenue_change' => $calcChange($currentRevenue, $prevRevenue),
                    'new_users' => $currentNewUsers,
                    'users_change' => $calcChange($currentNewUsers, $prevNewUsers),
                    'assessments_created' => $currentAssessments,
                    'assessments_change' => $calcChange($currentAssessments, $prevAssessments),
                    'tests_completed' => $currentTestsCompleted,
                    'tests_change' => $calcChange($currentTestsCompleted, $prevTests),
                ],
                'charts' => [
                    'revenue_trend' => $filledRevenue,
                    'user_growth' => $filledUsers,
                ],
                'transactions' => $recentTransactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load reports',
                'message' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    /**
     * Export reports as PDF
     */
    public function exportReportsPdf(Request $request)
    {
        $fromDate = $request->input('from') ? now()->parse($request->input('from')) : now()->subMonth();
        $toDate = $request->input('to') ? now()->parse($request->input('to'))->endOfDay() : now()->endOfDay();

        // Get stats
        $totalRevenue = Payment::where('status', 'success')
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->sum('amount');
        $newUsers = User::whereBetween('created_at', [$fromDate, $toDate])->count();
        $assessments = Assessment::whereBetween('created_at', [$fromDate, $toDate])->count();
        $testsCompleted = DB::table('invitees')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$fromDate, $toDate])
            ->count();

        // Recent transactions
        $transactions = Payment::with('user:id,first_name,last_name,email')
            ->where('status', 'success')
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->orderByDesc('paid_at')
            ->limit(50)
            ->get();

        // Generate HTML for PDF
        $html = view('admin.reports-pdf', [
            'fromDate' => $fromDate->format('M d, Y'),
            'toDate' => $toDate->format('M d, Y'),
            'totalRevenue' => $totalRevenue,
            'newUsers' => $newUsers,
            'assessments' => $assessments,
            'testsCompleted' => $testsCompleted,
            'transactions' => $transactions,
            'generatedAt' => now()->format('M d, Y H:i:s'),
        ])->render();

        // Return HTML that will be converted to PDF client-side
        return response()->json([
            'html' => $html,
            'filename' => "quizly-report-{$fromDate->format('Y-m-d')}-to-{$toDate->format('Y-m-d')}.pdf",
        ]);
    }

    // ========================================
    // SUBSCRIPTION PLAN CRUD
    // ========================================

    /**
     * List all subscription plans
     */
    public function subscriptionPlans(): JsonResponse
    {
        $plans = SubscriptionPlan::withCount(['payments as active_subscribers_count' => fn($q) => $q
            ->where('status', 'success')
            ->whereHas('user', fn($u) => $u->where('subscription_status', 'active'))
        ])->orderBy('monthly_price')->get();

        return response()->json(['data' => $plans]);
    }

    /**
     * Create a subscription plan
     */
    public function createPlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'monthly_price' => 'required|numeric|min:0',
            'annual_discount_percent' => 'sometimes|numeric|min:0|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['features'] = $validated['features'] ?? [];
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['annual_discount_percent'] = $validated['annual_discount_percent'] ?? 15.00;

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'message' => 'Subscription plan created successfully.',
            'plan' => $plan,
        ], 201);
    }

    /**
     * Update a subscription plan
     */
    public function updatePlan(Request $request, string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'monthly_price' => 'sometimes|numeric|min:0',
            'annual_discount_percent' => 'sometimes|numeric|min:0|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'message' => 'Subscription plan updated successfully.',
            'plan' => $plan->fresh(),
        ]);
    }

    /**
     * Delete a subscription plan
     */
    public function deletePlan(string $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        // Check if any active subscriptions use this plan
        $activeUsers = User::where('subscription_plan_id', $id)
            ->where('subscription_status', 'active')
            ->count();

        if ($activeUsers > 0) {
            return response()->json([
                'message' => "Cannot delete: {$activeUsers} users have active subscriptions on this plan.",
            ], 422);
        }

        $plan->delete();

        return response()->json(['message' => 'Subscription plan deleted successfully.']);
    }

    // ========================================
    // ASSESSMENT MANAGEMENT
    // ========================================

    /**
     * List all assessments across all users
     */
    public function assessments(Request $request): JsonResponse
    {
        $query = Assessment::with(['user:id,first_name,last_name,email,company_name'])
            ->withCount(['questions', 'invitees', 'invitees as completed_count' => fn($q) => $q->whereIn('status', ['completed'])])
            ->withAvg(['testSessions as avg_score' => fn($q) => $q->whereIn('status', ['submitted', 'completed', 'timed_out'])], 'percentage');

        // Search
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $assessments = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($assessments);
    }

    /**
     * Show full assessment details for admin
     */
    public function showAssessment(string $id): JsonResponse
    {
        $assessment = Assessment::with([
            'user:id,first_name,last_name,email,company_name',
            'questions.options',
            'invitees.testSession.answers',
        ])
        ->withCount(['invitees', 'invitees as completed_count' => fn($q) => $q->where('status', 'completed')])
        ->withAvg(['testSessions as avg_score' => fn($q) => $q->whereIn('status', ['submitted', 'completed', 'timed_out'])], 'percentage')
        ->findOrFail($id);

        return response()->json($assessment);
    }

    /**
     * Delete an assessment (admin override)
     */
    public function deleteAssessment(string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();

        return response()->json(['message' => 'Assessment deleted successfully.']);
    }
}

