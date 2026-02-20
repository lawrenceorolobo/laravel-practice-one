<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Assessment;
use App\Models\Invitee;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AssessmentController extends Controller
{
    /**
     * List all assessments for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $assessments = Assessment::where('user_id', $request->user()->id)
            ->withCount([
                'questions',
                'invitees',
                'testSessions',
                'invitees as completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->withAvg([
                'testSessions as avg_score' => fn($q) => $q->whereIn('status', ['submitted', 'completed', 'timed_out']),
            ], 'percentage')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($assessments);
    }

    /**
     * Create a new assessment
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'allow_back_navigation' => ['boolean'],
            'shuffle_questions' => ['boolean'],
            'shuffle_options' => ['boolean'],
            'show_result_to_taker' => ['boolean'],
            'proctoring_enabled' => ['boolean'],
            'webcam_required' => ['boolean'],
            'fullscreen_required' => ['boolean'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
        ]);

        $assessment = Assessment::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Assessment created successfully.',
            'assessment' => $assessment,
        ], 201);
    }

    /**
     * Get single assessment with questions
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['questions.options'])
            ->withCount([
                'invitees',
                'testSessions',
                'invitees as completed_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->withAvg([
                'testSessions as avg_score' => fn($q) => $q->whereIn('status', ['submitted', 'completed', 'timed_out']),
            ], 'percentage')
            ->firstOrFail();

        return response()->json($assessment);
    }

    /**
     * Update assessment
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($assessment->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => ['Only draft assessments can be edited.'],
            ]);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['sometimes', 'integer', 'min:5', 'max:480'],
            'pass_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'allow_back_navigation' => ['boolean'],
            'shuffle_questions' => ['boolean'],
            'shuffle_options' => ['boolean'],
            'show_result_to_taker' => ['boolean'],
            'proctoring_enabled' => ['boolean'],
            'webcam_required' => ['boolean'],
            'fullscreen_required' => ['boolean'],
            'start_datetime' => ['sometimes', 'date'],
            'end_datetime' => ['sometimes', 'date', 'after:start_datetime'],
        ]);

        $assessment->update($validated);

        return response()->json([
            'message' => 'Assessment updated successfully.',
            'assessment' => $assessment->fresh(),
        ]);
    }

    /**
     * Delete assessment
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!in_array($assessment->status, ['draft', 'cancelled'])) {
            throw ValidationException::withMessages([
                'status' => ['Only draft or cancelled assessments can be deleted.'],
            ]);
        }

        $assessment->delete();

        return response()->json([
            'message' => 'Assessment deleted successfully.',
        ]);
    }

    /**
     * Publish/schedule assessment
     */
    public function publish(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->withCount(['questions', 'invitees'])
            ->firstOrFail();

        if ($assessment->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => ['Only draft assessments can be published.'],
            ]);
        }

        if ($assessment->questions_count === 0) {
            throw ValidationException::withMessages([
                'questions' => ['Assessment must have at least one question before publishing.'],
            ]);
        }

        if ($assessment->invitees_count === 0) {
            throw ValidationException::withMessages([
                'invitees' => ['Assessment must have at least one candidate before publishing.'],
            ]);
        }

        // Update status + generate public access code
        $status = now()->gte($assessment->start_datetime) ? 'active' : 'scheduled';

        $assessment->update([
            'status' => $status,
            'total_questions' => $assessment->questions_count,
            'access_code' => $assessment->access_code ?? \Illuminate\Support\Str::random(24),
        ]);

        // Dispatch queued batch job (returns immediately)
        \App\Jobs\SendInvitationBatchJob::dispatch($assessment->id);

        return response()->json([
            'message' => "Assessment published. {$assessment->invitees_count} invitation(s) queued for delivery.",
            'assessment' => $assessment->fresh(),
            'invitations_queued' => $assessment->invitees_count,
        ]);
    }

    /**
     * Get assessment results
     */
    public function results(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $sessions = $assessment->testSessions()
            ->select([
                'id', 'first_name', 'last_name', 'email',
                'total_score', 'max_score', 'percentage', 'passed',
                'time_spent_seconds', 'started_at', 'submitted_at', 'status',
                'fullscreen_exits', 'tab_switches',
            ])
            ->orderByDesc('percentage')
            ->paginate(50);

        return response()->json($sessions);
    }

    /**
     * Get assessment analytics
     */
    public function analytics(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $sessions = $assessment->testSessions()
            ->whereIn('status', ['submitted', 'timed_out'])
            ->get();

        $totalTakers = $sessions->count();
        $passed = $sessions->where('passed', true)->count();
        $failed = $totalTakers - $passed;
        $avgScore = $sessions->avg('percentage') ?? 0;
        $avgTime = $sessions->avg('time_spent_seconds') ?? 0;
        $totalInvites = $assessment->invitees()->count();
        $responseRate = $totalInvites > 0 ? ($totalTakers / $totalInvites) * 100 : 0;

        // Question difficulty analysis
        $questionStats = DB::table('test_answers')
            ->join('questions', 'test_answers.question_id', '=', 'questions.id')
            ->where('questions.assessment_id', $id)
            ->groupBy('questions.id', 'questions.question_text')
            ->select([
                'questions.id',
                DB::raw('SUBSTRING(questions.question_text, 1, 100) as question_preview'),
                DB::raw('COUNT(*) as total_answers'),
                DB::raw('SUM(CASE WHEN test_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('AVG(CASE WHEN test_answers.is_correct = 1 THEN 100 ELSE 0 END) as success_rate'),
            ])
            ->get();

        return response()->json([
            'summary' => [
                'total_invites' => $totalInvites,
                'total_takers' => $totalTakers,
                'response_rate' => round($responseRate, 1),
                'passed' => $passed,
                'failed' => $failed,
                'pass_rate' => $totalTakers > 0 ? round(($passed / $totalTakers) * 100, 1) : 0,
                'average_score' => round($avgScore, 1),
                'average_time_minutes' => round($avgTime / 60, 1),
            ],
            'charts' => [
                'pass_fail' => [
                    ['label' => 'Passed', 'value' => $passed],
                    ['label' => 'Failed', 'value' => $failed],
                ],
                'score_distribution' => $this->getScoreDistribution($sessions),
            ],
            'question_analysis' => $questionStats,
        ]);
    }

    protected function getScoreDistribution($sessions): array
    {
        $ranges = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        foreach ($sessions as $session) {
            $score = $session->percentage ?? 0;
            if ($score <= 20) $ranges['0-20']++;
            elseif ($score <= 40) $ranges['21-40']++;
            elseif ($score <= 60) $ranges['41-60']++;
            elseif ($score <= 80) $ranges['61-80']++;
            else $ranges['81-100']++;
        }

        return collect($ranges)->map(fn ($v, $k) => ['label' => $k, 'value' => $v])->values()->toArray();
    }
}
