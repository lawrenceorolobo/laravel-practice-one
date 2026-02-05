<?php

namespace App\Http\Controllers\Api;

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
            ->with('assessment:id,title,description,duration_minutes,start_datetime,end_datetime,status')
            ->firstOrFail();

        // Mark as opened if first time
        $invitee->markAsOpened();

        $assessment = $invitee->assessment;

        // Check if assessment is active
        if (!$assessment->isActive()) {
            $message = match (true) {
                $assessment->start_datetime->isFuture() => 'This assessment has not started yet.',
                $assessment->end_datetime->isPast() => 'This assessment has ended.',
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
            ],
            'email' => $invitee->email,
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
            ->with(['assessment.questions.options', 'testSession'])
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

        // Get answered question IDs
        $answeredIds = $session->answers()->pluck('question_id')->toArray();

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
                'answered' => in_array($q->id, $answeredIds),
                'options' => $options->map(fn ($o) => [
                    'label' => $o->option_label,
                    'text' => $o->option_text,
                ])->values(),
            ];
        });

        return response()->json([
            'questions' => $questionsData->values(),
            'total' => $questions->count(),
            'answered' => count($answeredIds),
            'time_remaining' => max(0, $timeLimit - $timeSpent),
            'allow_back_navigation' => $assessment->allow_back_navigation,
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
            'selected_options.*' => ['string', 'size:1'],
            'text_answer' => ['nullable', 'string', 'max:5000'],
        ]);

        $invitee = Invitee::where('invite_token', $token)
            ->with(['testSession', 'assessment'])
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

        // Use idempotent upsert
        $answer = TestAnswer::updateOrCreate(
            [
                'session_id' => $session->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_options' => $validated['selected_options'] ?? null,
                'text_answer' => $validated['text_answer'] ?? null,
                'answered_at' => now(),
            ]
        );

        // Calculate correctness immediately for MCQ
        $answer->checkCorrectness();

        return response()->json([
            'message' => 'Answer saved.',
            'answered' => $session->answers()->count(),
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

        // Notify business admin via WebSocket and Email
        \App\Events\AssessmentCompleted::dispatch($invitee->assessment, [
            'score' => $session->percentage,
            'candidate_name' => "{$invitee->first_name} {$invitee->last_name}",
        ]);

        return response()->json($result);
    }

    /**
     * Log proctoring event (tab switch, fullscreen exit)
     */
    public function logProctoringEvent(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'in:tab_switch,fullscreen_exit'],
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
}
