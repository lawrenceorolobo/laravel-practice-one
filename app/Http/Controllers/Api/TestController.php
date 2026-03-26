<?php

namespace App\Http\Controllers\Api;

use App\Events\InviteeUpdated;
use App\Events\TestCompleted;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Invitee;
use App\Models\TestSession;
use App\Models\TestAnswer;
use App\Services\FraudDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestController extends Controller
{
    public function __construct(
        protected FraudDetectionService $fraudDetection
    ) {}

    /**
     * Validate invite token and get assessment info
     */
    public function validateToken(Request $request, string $token): JsonResponse
    {
        $invitee = Invitee::where('invite_token', $token)
            ->with(['assessment:id,title,description,duration_minutes,start_datetime,end_datetime,status,auto_end_on_leave,webcam_required,proctoring_enabled,fullscreen_required,allow_back_navigation,shuffle_questions,shuffle_options', 'testSession'])
            ->firstOrFail();

        // Mark as opened if first time
        $invitee->markAsOpened();

        $assessment = $invitee->assessment;

        // Check if assessment is active
        if (!$assessment->isActive()) {
            $message = match (true) {
                $assessment->start_datetime && $assessment->start_datetime->isFuture() => 'This assessment has not started yet.',
                ($assessment->end_datetime && $assessment->end_datetime->isPast()) => 'This assessment has ended.',
                default => 'This assessment is not currently available.',
            };

            return response()->json([
                'valid' => false,
                'message' => $message,
                'start_datetime' => $assessment->start_datetime,
                'end_datetime' => $assessment->end_datetime,
            ], 403);
        }

        // Check if already completed
        if ($invitee->hasCompleted()) {
            return response()->json([
                'valid' => false,
                'message' => 'You have already completed this assessment.',
            ], 403);
        }

        // Check for existing in-progress session
        $existingSession = $invitee->testSession;
        if ($existingSession && $existingSession->isInProgress()) {
            return response()->json([
                'valid' => true,
                'resume' => true,
                'session_id' => $existingSession->id,
                'assessment' => [
                    'title' => $assessment->title,
                    'description' => $assessment->description,
                    'duration_minutes' => $assessment->duration_minutes,
                    'auto_end_on_leave' => (bool) $assessment->auto_end_on_leave,
                    'webcam_required' => (bool) $assessment->webcam_required,
                    'proctoring_enabled' => (bool) $assessment->proctoring_enabled,
                    'fullscreen_required' => (bool) $assessment->fullscreen_required,
                ],
                'email' => $invitee->email,
                'started_at' => $existingSession->started_at,
                'time_remaining' => max(0, $assessment->duration_minutes * 60 - now()->diffInSeconds($existingSession->started_at)),
            ]);
        }

        return response()->json([
            'valid' => true,
            'resume' => false,
            'assessment' => [
                'title' => $assessment->title,
                'description' => $assessment->description,
                'duration_minutes' => $assessment->duration_minutes,
                'auto_end_on_leave' => (bool) $assessment->auto_end_on_leave,
                'webcam_required' => (bool) $assessment->webcam_required,
                'proctoring_enabled' => (bool) $assessment->proctoring_enabled,
                'fullscreen_required' => (bool) $assessment->fullscreen_required,
            ],
            'email' => $invitee->email,
            'first_name' => $invitee->first_name,
            'last_name' => $invitee->last_name,
        ]);
    }

    /**
     * Start test session with fingerprinting and fraud detection
     */
    public function startSession(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
            'canvas_fingerprint' => ['nullable', 'string', 'max:255'],
            'webgl_fingerprint' => ['nullable', 'string', 'max:255'],
            'screen_resolution' => ['nullable', 'string', 'max:50'],
            'timezone' => ['nullable', 'string', 'max:100'],
        ]);

        $invitee = Invitee::where('invite_token', $token)
            ->with('assessment')
            ->firstOrFail();

        if (!$invitee->assessment->isActive()) {
            throw ValidationException::withMessages([
                'assessment' => ['This assessment is not currently available.'],
            ]);
        }

        if ($invitee->hasCompleted()) {
            throw ValidationException::withMessages([
                'assessment' => ['You have already completed this assessment.'],
            ]);
        }

        // Return existing session if in progress
        $existingSession = $invitee->testSession;
        if ($existingSession && $existingSession->isInProgress()) {
            return response()->json([
                'message' => 'Session resumed.',
                'session_id' => $existingSession->id,
            ]);
        }

        // Fraud detection
        $fraudCheck = $this->fraudDetection->checkForFraud(
            $invitee->assessment_id,
            $invitee->email,
            $validated['first_name'],
            $validated['last_name'],
            $request->ip(),
            $validated['device_fingerprint'] ?? null
        );

        if ($fraudCheck['blocked']) {
            throw ValidationException::withMessages([
                'fraud' => [$fraudCheck['reason']],
            ]);
        }

        // Use distributed lock to prevent race conditions
        $lockKey = "start_test:{$invitee->id}";
        $lock = cache()->lock($lockKey, 30);

        if (!$lock->get()) {
            throw ValidationException::withMessages([
                'session' => ['Please wait, your session is being created.'],
            ]);
        }

        try {
            // Double-check no session was created while waiting
            if ($invitee->fresh()->testSession) {
                return response()->json([
                    'message' => 'Session already exists.',
                    'session_id' => $invitee->testSession->id,
                ]);
            }

            $session = DB::transaction(function () use ($invitee, $validated, $request) {
                $session = TestSession::create([
                    'invitee_id' => $invitee->id,
                    'assessment_id' => $invitee->assessment_id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $invitee->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device_fingerprint' => $validated['device_fingerprint'] ?? null,
                    'canvas_fingerprint' => $validated['canvas_fingerprint'] ?? null,
                    'webgl_fingerprint' => $validated['webgl_fingerprint'] ?? null,
                    'screen_resolution' => $validated['screen_resolution'] ?? null,
                    'timezone' => $validated['timezone'] ?? null,
                    'started_at' => now(),
                    'status' => 'in_progress',
                ]);

                $invitee->update(['status' => 'started']);

                return $session;
            });

            return response()->json([
                'message' => 'Session started successfully.',
                'session_id' => $session->id,
            ], 201);
        } finally {
            $lock->release();
        }
    }

    /**
     * Get questions for test session
     */
    public function getQuestions(Request $request, string $token): JsonResponse
    {
        $invitee = Invitee::where('invite_token', $token)
            ->with(['assessment.questions.options', 'testSession.answers:id,session_id,question_id'])
            ->firstOrFail();

        $session = $invitee->testSession;
        if (!$session || !$session->isInProgress()) {
            throw ValidationException::withMessages([
                'session' => ['No active session found.'],
            ]);
        }

        // Check time limit
        $timeSpent = now()->diffInSeconds($session->started_at);
        $timeLimit = $invitee->assessment->duration_minutes * 60;

        if ($timeSpent > $timeLimit) {
            $this->autoSubmit($session);
            throw ValidationException::withMessages([
                'time' => ['Time limit exceeded. Your answers have been submitted.'],
            ]);
        }

        $assessment = $invitee->assessment;
        $questions = $assessment->questions;

        // Shuffle if enabled
        if ($assessment->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Get answered question IDs (preloaded via eager-load)
        $answeredIds = $session->answers->pluck('question_id')->toArray();

        $questionsData = $questions->map(function ($q) use ($assessment, $answeredIds) {
            $options = $q->options;
            
            if ($assessment->shuffle_options) {
                $options = $options->shuffle();
            }

            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'question_type' => $q->question_type,
                'points' => $q->points,
                'question_metadata' => $q->question_metadata,
                'answered' => in_array($q->id, $answeredIds),
                'options' => $options->map(fn ($o) => [
                    'label' => $o->option_label,
                    'text' => $o->option_text,
                    'media_url' => $o->media_url ?? null,
                    'media_type' => $o->media_type ?? null,
                ])->values(),
            ];
        });

        return response()->json([
            'questions' => $questionsData->values(),
            'total' => $questions->count(),
            'answered' => count($answeredIds),
            'time_remaining' => max(0, $timeLimit - $timeSpent),
            'allow_back_navigation' => $assessment->allow_back_navigation,
            'question_navigation' => feature('question_navigation'),
        ]);
    }

    /**
     * Submit answer for a question
     */
    public function submitAnswer(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'uuid'],
            'selected_options' => ['nullable', 'array'],
            'selected_options.*' => ['string', 'max:500'],
            'text_answer' => ['nullable', 'string', 'max:5000'],
            'ordering' => ['nullable', 'array'],
            'ordering.*' => ['string'],
            'matching' => ['nullable', 'array'],
            'puzzle' => ['nullable', 'array'],
            'time_spent_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
        ]);

        $invitee = Invitee::where('invite_token', $token)
            ->select(['id', 'invite_token', 'assessment_id', 'status'])
            ->with(['testSession:id,invitee_id,status,started_at', 'assessment:id,duration_minutes'])
            ->firstOrFail();

        $session = $invitee->testSession;
        if (!$session || !$session->isInProgress()) {
            throw ValidationException::withMessages([
                'session' => ['No active session found.'],
            ]);
        }

        // Check time
        $timeSpent = now()->diffInSeconds($session->started_at);
        $timeLimit = $invitee->assessment->duration_minutes * 60;

        if ($timeSpent > $timeLimit) {
            $this->autoSubmit($session);
            throw ValidationException::withMessages([
                'time' => ['Time limit exceeded.'],
            ]);
        }

        // Store ordering/matching via existing columns
        $selectedOptions = $validated['selected_options'] ?? null;
        $textAnswer = $validated['text_answer'] ?? null;

        // Ordering: store as JSON array in selected_options
        if (!empty($validated['ordering'])) {
            $selectedOptions = $validated['ordering'];
        }

        // Matching: store as JSON string in text_answer
        if (!empty($validated['matching'])) {
            $textAnswer = json_encode($validated['matching']);
        }

        // Puzzle: store as JSON string in text_answer (slot → piece mapping)
        if (!empty($validated['puzzle'])) {
            $textAnswer = json_encode($validated['puzzle']);
        }

        // Use idempotent upsert
        $answer = TestAnswer::updateOrCreate(
            [
                'session_id' => $session->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_options' => $selectedOptions,
                'text_answer' => $textAnswer,
                'answered_at' => now(),
                'time_spent_seconds' => $validated['time_spent_seconds'] ?? null,
            ]
        );

        // Calculate correctness immediately for MCQ
        $answer->checkCorrectness();

        return response()->json([
            'message' => 'Answer saved.',
            'answered' => DB::table('test_answers')->where('session_id', $session->id)->count(),
        ]);
    }

    /**
     * Final submission
     */
    public function submit(Request $request, string $token): JsonResponse
    {
        $invitee = Invitee::where('invite_token', $token)
            ->with(['testSession', 'assessment'])
            ->firstOrFail();

        $session = $invitee->testSession;
        if (!$session || !$session->isInProgress()) {
            throw ValidationException::withMessages([
                'session' => ['No active session found.'],
            ]);
        }

        $session->calculateScore();
        $invitee->update(['status' => 'completed']);

        $result = [
            'message' => 'Assessment submitted successfully.',
            'score' => null,
        ];

        if ($invitee->assessment->show_result_to_taker) {
            $result['score'] = [
                'total' => $session->total_score,
                'max' => $session->max_score,
                'percentage' => $session->percentage,
                'passed' => $session->passed,
            ];
        }

        // Load assessment with user for emails and broadcasting
        $assessment = $invitee->assessment->load('user');

        // Send separate result emails (queued) — only if email notifications enabled
        if (feature('email_notifications')) {
            dispatch(function () use ($assessment, $invitee, $session) {
                try {
                    // Email to assessment creator (business admin)
                    $ownerMail = new \App\Mail\AssessmentResultMail($assessment, $invitee, $session);
                    \Illuminate\Support\Facades\Mail::to($assessment->user->email)->send($ownerMail);

                    // Separate email to candidate (only if flag enabled)
                    if (feature('send_answers_to_taker')) {
                        $candidateMail = new \App\Mail\CandidateResultMail($assessment, $invitee, $session);
                        \Illuminate\Support\Facades\Mail::to($invitee->email)->send($candidateMail);
                    }
                } catch (\Exception $e) {
                    \Log::warning("Result email failed: " . $e->getMessage());
                }
            });
        }

        // Broadcast real-time update
        broadcast(new TestCompleted(
            $invitee->assessment_id,
            $assessment->user_id,
            $invitee->email
        ));
        broadcast(new InviteeUpdated($invitee->assessment_id, 'completed'));

        return response()->json($result);
    }

    /**
     * Log proctoring event (tab switch, fullscreen exit)
     */
    public function logProctoringEvent(Request $request, string $token): JsonResponse
    {
        if (!feature('proctoring_enabled')) {
            return response()->json(['message' => 'Event logged.']);
        }

        $validated = $request->validate([
            'event_type' => ['required', 'in:tab_switch,fullscreen_exit,hardware_disconnected'],
        ]);

        $invitee = Invitee::where('invite_token', $token)
            ->with('testSession')
            ->firstOrFail();

        $session = $invitee->testSession;
        if (!$session || !$session->isInProgress()) {
            return response()->json(['message' => 'Event logged.']);
        }

        if ($validated['event_type'] === 'tab_switch') {
            $session->incrementTabSwitch();
        } else {
            $session->incrementFullscreenExit();
        }

        return response()->json(['message' => 'Event logged.']);
    }

    protected function autoSubmit(TestSession $session): void
    {
        if ($session->isInProgress()) {
            $session->calculateScore();
            $session->update(['status' => 'timed_out']);
            $session->invitee->update(['status' => 'completed']);
        }
    }

    /**
     * Validate public access code and return assessment info
     */
    public function validateAccessCode(Request $request, string $accessCode): JsonResponse
    {
        $assessment = Assessment::where('access_code', $accessCode)
            ->whereIn('status', ['active', 'scheduled'])
            ->first();

        if (!$assessment) {
            return response()->json([
                'valid' => false,
                'message' => 'This assessment link is invalid or has expired.',
            ], 404);
        }

        if (!$assessment->isActive()) {
            $message = match (true) {
                $assessment->start_datetime && $assessment->start_datetime->isFuture() => 'This assessment has not started yet. It opens on ' . $assessment->start_datetime->format('M d, Y \a\t g:i A') . '.',
                ($assessment->end_datetime && $assessment->end_datetime->isPast()) => 'This assessment has ended.',
                default => 'This assessment is not currently available.',
            };

            return response()->json([
                'valid' => false,
                'message' => $message,
            ], 403);
        }

        return response()->json([
            'valid' => true,
            'assessment' => [
                'title' => $assessment->title,
                'description' => $assessment->description,
                'duration_minutes' => $assessment->duration_minutes,
                'start_datetime' => $assessment->start_datetime,
                'end_datetime' => $assessment->end_datetime,
            ],
        ]);
    }

    /**
     * Join assessment via public access code (creates invitee + returns token)
     */
    public function joinByAccessCode(Request $request, string $accessCode): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
        ]);

        $assessment = Assessment::where('access_code', $accessCode)
            ->whereIn('status', ['active', 'scheduled'])
            ->first();

        if (!$assessment || !$assessment->isActive()) {
            return response()->json([
                'message' => 'This assessment is not available.',
            ], 404);
        }

        $email = strtolower(trim($validated['email']));

        // Check if already invited/started/completed
        $existing = Invitee::where('assessment_id', $assessment->id)
            ->where(DB::raw('LOWER(email)'), $email)
            ->first();

        if ($existing) {
            if ($existing->hasCompleted()) {
                return response()->json([
                    'message' => 'You have already completed this assessment.',
                ], 409);
            }

            if ($existing->hasStarted()) {
                return response()->json([
                    'message' => 'You have already started this assessment. Use your original link to continue.',
                    'redirect_token' => $existing->invite_token,
                ], 409);
            }

            // Already invited but hasn't started — redirect to their token
            return response()->json([
                'message' => 'You are already registered for this assessment.',
                'redirect_token' => $existing->invite_token,
            ]);
        }

        // Create new invitee with HMAC-signed token
        $rawToken = \Illuminate\Support\Str::random(48);
        $signature = hash_hmac('sha256', $rawToken . $email . $assessment->id, config('app.key'));
        $inviteToken = $rawToken . '_' . substr($signature, 0, 16);

        $invitee = Invitee::create([
            'assessment_id' => $assessment->id,
            'email' => $email,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'invite_token' => $inviteToken,
            'status' => 'pending',
        ]);

        $assessment->update(['total_invites' => $assessment->invitees()->count()]);

        return response()->json([
            'message' => 'Successfully registered for assessment.',
            'redirect_token' => $invitee->invite_token,
        ], 201);
    }

    /**
     * Save webcam recording URL from Cloudinary upload
     */
    public function saveRecording(Request $request, string $token): JsonResponse
    {
        if (!feature('webcam_recording')) {
            return response()->json(['message' => 'Recording saved.']);
        }

        $invitee = Invitee::where('invite_token', $token)->firstOrFail();
        
        // Accept both in_progress and submitted/timed_out — recording may arrive slightly after submission
        $session = TestSession::where('invitee_id', $invitee->id)
            ->whereIn('status', ['in_progress', 'submitted', 'timed_out'])
            ->latest()
            ->firstOrFail();

        $validated = $request->validate([
            'recording_url' => ['required', 'url', 'max:500'],
            'recording_id' => ['required', 'string', 'max:255'],
        ]);

        $session->update([
            'webcam_recording_url' => $validated['recording_url'],
            'webcam_recording_id' => $validated['recording_id'],
        ]);

        return response()->json(['message' => 'Recording saved.']);
    }
}
