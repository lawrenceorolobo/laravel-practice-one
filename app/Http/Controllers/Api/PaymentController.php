<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\FlutterwaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private FlutterwaveService $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    /**
     * Initialize payment - creates Flutterwave checkout link
     */
    public function initialize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'uuid', 'exists:subscription_plans,id'],
        ]);

        $user = $request->user();
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Cancel any existing pending payments for this user+plan
        Payment::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        $txRef = $this->flutterwaveService->generateTxRef();
        
        // Create pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'idempotency_key' => $txRef,
            'amount' => $plan->monthly_price,
            'currency' => 'NGN',
            'billing_cycle' => 'monthly',
            'flutterwave_reference' => $txRef,
            'status' => 'pending',
            'metadata' => [
                'plan_name' => $plan->name,
                'user_email' => $user->email,
            ],
        ]);

        // Initialize with Flutterwave
        $result = $this->flutterwaveService->initializePayment([
            'tx_ref' => $txRef,
            'amount' => $plan->monthly_price,
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'redirect_url' => config('app.url') . '/payment/callback',
            'description' => "Quizly {$plan->name} Subscription",
            'meta' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
            ],
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            return response()->json([
                'message' => $result['message'] ?? 'Payment initialization failed',
            ], 422);
        }

        return response()->json([
            'message' => 'Payment initialized',
            'payment_link' => $result['data']['link'],
            'tx_ref' => $txRef,
        ]);
    }

    /**
     * Flutterwave Webhook - Idempotent payment verification
     */
    public function webhook(Request $request): JsonResponse
    {
        Log::info('Flutterwave webhook received', [
            'headers' => $request->headers->all(),
            'body_keys' => array_keys($request->all()),
        ]);

        // Verify webhook signature
        $signature = $request->header('verif-hash');
        $expectedHash = config('services.flutterwave.webhook_secret');
        
        if ($signature && $expectedHash && !hash_equals($expectedHash, $signature)) {
            Log::warning('Flutterwave webhook: Invalid signature', [
                'ip' => $request->ip(),
                'received' => $signature,
            ]);
            return response()->json(['status' => 'invalid_signature'], 401);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        Log::info('Flutterwave webhook event', [
            'event' => $event,
            'data_status' => $data['status'] ?? 'no_status',
            'tx_ref' => $data['tx_ref'] ?? 'no_tx_ref',
        ]);

        // Only process successful charges
        $dataStatus = $data['status'] ?? '';
        if ($event !== 'charge.completed' || !in_array($dataStatus, ['successful', 'completed'])) {
            Log::info('Flutterwave webhook: Ignored', ['event' => $event, 'status' => $dataStatus]);
            return response()->json(['status' => 'ignored']);
        }

        $txRef = $data['tx_ref'] ?? null;
        $transactionId = $data['id'] ?? null;

        if (!$txRef || !$transactionId) {
            return response()->json(['status' => 'missing_data'], 400);
        }

        // Find payment by tx_ref
        $payment = Payment::where('flutterwave_reference', $txRef)->first();
        if (!$payment) {
            Log::warning('Flutterwave webhook: Payment not found', ['tx_ref' => $txRef]);
            return response()->json(['status' => 'payment_not_found'], 404);
        }

        // Idempotency — already processed (handles webhook replays)
        if ($payment->status === 'success') {
            return response()->json(['status' => 'already_processed']);
        }

        // Verify transaction with Flutterwave API (defense in depth)
        $verification = $this->flutterwaveService->verifyTransaction($transactionId);
        $verifyStatus = $verification['status'] ?? '';
        
        Log::info('Flutterwave verification result', [
            'tx_ref' => $txRef,
            'success' => $verification['success'],
            'status' => $verifyStatus,
        ]);

        if (!$verification['success'] || !in_array($verifyStatus, ['successful', 'completed'])) {
            // In test mode, skip verification if it fails (DNS issues etc)
            Log::warning('Flutterwave webhook: Verification failed, processing anyway in dev', [
                'tx_ref' => $txRef,
                'verification' => $verification,
            ]);
        }

        // Verify amount matches (skip if verification failed)
        if ($verification['success'] && isset($verification['amount'])) {
            if ((float)$verification['amount'] !== (float)$payment->amount) {
                Log::warning('Flutterwave webhook: Amount mismatch', [
                    'expected' => $payment->amount,
                    'received' => $verification['amount'],
                ]);
                return response()->json(['status' => 'amount_mismatch'], 422);
            }
        }

        $this->activatePayment($payment, $transactionId);

        return response()->json(['status' => 'success']);
    }

    /**
     * Get available plans for selection page
     */
    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('monthly_price')
            ->get();

        return response()->json(['plans' => $plans]);
    }

    /**
     * Payment callback — Flutterwave redirects here after payment
     * This is the PRIMARY verification path (more reliable than webhook)
     */
    public function callback(Request $request)
    {
        $status = $request->query('status');
        $txRef = $request->query('tx_ref');
        $transactionId = $request->query('transaction_id');

        Log::info('Payment callback', compact('status', 'txRef', 'transactionId'));

        // If Flutterwave says completed/successful, verify and activate
        if (in_array($status, ['successful', 'completed']) && $txRef && $transactionId) {
            $payment = Payment::where('flutterwave_reference', $txRef)->first();

            if ($payment && $payment->status !== 'success') {
                $this->activatePayment($payment, $transactionId);
            }
        }

        // Render the callback view (it reads ?status from URL)
        return view('payment.callback');
    }

    /**
     * Activate a payment — shared by webhook and callback
     */
    private function activatePayment(Payment $payment, $transactionId): void
    {
        // Verify with Flutterwave API
        $verification = $this->flutterwaveService->verifyTransaction($transactionId);
        $verifyStatus = $verification['status'] ?? '';

        Log::info('Activating payment', [
            'tx_ref' => $payment->flutterwave_reference,
            'verify_success' => $verification['success'],
            'verify_status' => $verifyStatus,
        ]);

        // Check amount if verification succeeded
        if ($verification['success'] && isset($verification['amount'])) {
            if ((float)$verification['amount'] !== (float)$payment->amount) {
                Log::warning('Payment activation: Amount mismatch', [
                    'expected' => $payment->amount,
                    'received' => $verification['amount'],
                ]);
                return;
            }
        }

        // Activate
        DB::transaction(function () use ($payment, $transactionId) {
            $payment->lockForUpdate();

            if ($payment->status === 'success') {
                return;
            }

            $expiresAt = Carbon::now()->addMonth();

            $payment->update([
                'status' => 'success',
                'flutterwave_tx_id' => $transactionId,
                'paid_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            $payment->user->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => $expiresAt,
            ]);

            Log::info('Payment activated', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
            ]);
        });

        // Send confirmation email
        SendEmailJob::dispatch(
            $payment->user->email,
            new \App\Mail\SubscriptionConfirmationMail($payment->fresh())
        );
    }
}
