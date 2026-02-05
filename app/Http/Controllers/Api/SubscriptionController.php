<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    protected string $paystackUrl = 'https://api.paystack.co';

    /**
     * Get available subscription plans
     */
    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return response()->json([
            'plans' => $plans->map(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'monthly_price' => $plan->monthly_price,
                'annual_price' => $plan->annual_price,
                'annual_discount_percent' => $plan->annual_discount_percent,
                'features' => $plan->features,
            ]),
        ]);
    }

    /**
     * Get current subscription status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        $latestPayment = $user->payments()
            ->where('status', 'success')
            ->latest()
            ->first();

        return response()->json([
            'subscription_status' => $user->subscription_status,
            'expires_at' => $user->subscription_expires_at,
            'has_active' => $user->hasActiveSubscription(),
            'latest_payment' => $latestPayment ? [
                'amount' => $latestPayment->amount,
                'billing_cycle' => $latestPayment->billing_cycle,
                'paid_at' => $latestPayment->paid_at,
            ] : null,
        ]);
    }

    /**
     * Initialize Paystack payment
     */
    public function initialize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'uuid', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,annual'],
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Calculate amount
        $amount = $validated['billing_cycle'] === 'annual' 
            ? $plan->annual_price 
            : $plan->monthly_price;

        // Generate idempotency key
        $idempotencyKey = Str::uuid()->toString();

        // Check for existing pending payment (prevent duplicates)
        $lockKey = "payment_init:{$user->id}";
        $lock = Cache::lock($lockKey, 30);

        if (!$lock->get()) {
            throw ValidationException::withMessages([
                'payment' => ['A payment is already being processed.'],
            ]);
        }

        try {
            // Create payment record first
            $payment = Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'idempotency_key' => $idempotencyKey,
                'amount' => $amount,
                'currency' => 'NGN',
                'billing_cycle' => $validated['billing_cycle'],
                'status' => 'pending',
                'expires_at' => $this->calculateExpiryDate($validated['billing_cycle']),
            ]);

            // Initialize Paystack transaction
            $response = Http::withToken(config('services.paystack.secret_key'))
                ->post("{$this->paystackUrl}/transaction/initialize", [
                    'email' => $user->email,
                    'amount' => (int) ($amount * 100), // Paystack uses kobo
                    'currency' => 'NGN',
                    'reference' => $payment->id,
                    'callback_url' => config('app.url') . '/payment/callback',
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'billing_cycle' => $validated['billing_cycle'],
                    ],
                ]);

            if (!$response->successful() || !$response->json('status')) {
                $payment->update(['status' => 'failed']);
                throw ValidationException::withMessages([
                    'payment' => ['Failed to initialize payment. Please try again.'],
                ]);
            }

            $data = $response->json('data');

            $payment->update([
                'paystack_reference' => $data['reference'],
            ]);

            return response()->json([
                'message' => 'Payment initialized.',
                'authorization_url' => $data['authorization_url'],
                'reference' => $data['reference'],
            ]);
        } finally {
            $lock->release();
        }
    }

    /**
     * Verify payment (callback from frontend)
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['required', 'string'],
        ]);

        $payment = Payment::where('paystack_reference', $validated['reference'])->first();

        if (!$payment) {
            throw ValidationException::withMessages([
                'reference' => ['Payment not found.'],
            ]);
        }

        if ($payment->status === 'success') {
            return response()->json([
                'message' => 'Payment already verified.',
                'status' => 'success',
            ]);
        }

        // Verify with Paystack
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("{$this->paystackUrl}/transaction/verify/{$validated['reference']}");

        if (!$response->successful()) {
            throw ValidationException::withMessages([
                'payment' => ['Unable to verify payment.'],
            ]);
        }

        $data = $response->json('data');

        if ($data['status'] === 'success') {
            $this->activateSubscription($payment, $data);
            
            return response()->json([
                'message' => 'Payment successful.',
                'status' => 'success',
            ]);
        }

        $payment->update(['status' => 'failed']);

        return response()->json([
            'message' => 'Payment was not successful.',
            'status' => $data['status'],
        ], 400);
    }

    /**
     * Handle Paystack webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();
        $computedSignature = hash_hmac('sha512', $payload, config('services.paystack.secret_key'));

        if (!hash_equals($computedSignature, $signature)) {
            logger()->warning('Invalid Paystack webhook signature', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $payment = Payment::where('paystack_reference', $data['reference'])->first();
            
            if ($payment && $payment->status !== 'success') {
                $this->activateSubscription($payment, $data);
            }
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    protected function activateSubscription(Payment $payment, array $paystackData): void
    {
        DB::transaction(function () use ($payment, $paystackData) {
            $payment->update([
                'status' => 'success',
                'paystack_transaction_id' => $paystackData['id'] ?? null,
                'paid_at' => now(),
                'metadata' => $paystackData,
            ]);

            $user = $payment->user;
            $user->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => $payment->expires_at,
            ]);
        });

        // TODO: Send confirmation email
        logger()->info('Subscription activated', [
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
        ]);
    }

    protected function calculateExpiryDate(string $billingCycle): \Carbon\Carbon
    {
        return $billingCycle === 'annual' 
            ? now()->addYear() 
            : now()->addMonth();
    }
}
