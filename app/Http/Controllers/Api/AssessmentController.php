<?php

namespace App\Http\Controllers\Api;

use App\Events\AssessmentUpdated;
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

        // Override proctoring fields if feature flags are disabled
        if (!feature('proctoring_enabled')) {
            $validated['proctoring_enabled'] = false;
            $validated['fullscreen_required'] = false;
        }
        if (!feature('webcam_recording')) {
            $validated['webcam_required'] = false;
        }

        $assessment = Assessment::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'draft',
        ]);

        broadcast(new AssessmentUpdated($request->user()->id, 'created'))->toOthers();

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

        // Override proctoring fields if feature flags are disabled
        if (!feature('proctoring_enabled')) {
            $validated['proctoring_enabled'] = false;
            $validated['fullscreen_required'] = false;
        }
        if (!feature('webcam_recording')) {
            $validated['webcam_required'] = false;
        }

        $assessment->update($validated);

        broadcast(new AssessmentUpdated($request->user()->id, 'updated'))->toOthers();

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

        broadcast(new AssessmentUpdated($request->user()->id, 'deleted'))->toOthers();

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

        broadcast(new AssessmentUpdated($request->user()->id, 'published'))->toOthers();

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

        // Single aggregate query instead of loading all sessions into memory
        $stats = DB::selectOne("
            SELECT
                COUNT(*) as total_takers,
                SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed,
                COALESCE(AVG(percentage), 0) as avg_score,
                COALESCE(AVG(time_spent_seconds), 0) as avg_time
            FROM test_sessions
            WHERE assessment_id = ? AND status IN ('submitted','timed_out')
        ", [$id]);

        $totalTakers = (int) ($stats->total_takers ?? 0);
        $passed = (int) ($stats->passed ?? 0);
        $failed = $totalTakers - $passed;
        $avgScore = (float) ($stats->avg_score ?? 0);
        $avgTime = (float) ($stats->avg_time ?? 0);

        $totalInvites = (int) DB::selectOne(
            "SELECT COUNT(*) as cnt FROM invitees WHERE assessment_id = ?", [$id]
        )->cnt;
        $responseRate = $totalInvites > 0 ? ($totalTakers / $totalInvites) * 100 : 0;

        // Question difficulty + score distribution in parallel queries
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

        // Score distribution via SQL instead of loading all rows
        $scoreDist = $this->getScoreDistribution($id);

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
                'score_distribution' => $scoreDist,
            ],
            'question_analysis' => $questionStats,
        ]);
    }

    /**
     * Get detailed answers for a specific test session
     */
    public function sessionAnswers(Request $request, string $id, string $sessionId): JsonResponse
    {
        $assessment = Assessment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $session = $assessment->testSessions()
            ->where('id', $sessionId)
            ->whereIn('status', ['submitted', 'completed', 'timed_out'])
            ->firstOrFail();

        $answers = DB::select("
            SELECT
                ta.id as answer_id,
                q.question_text,
                q.question_type,
                q.points,
                ta.selected_options,
                ta.text_answer,
                ta.is_correct,
                ta.points_earned,
                ta.answered_at
            FROM test_answers ta
            JOIN questions q ON q.id = ta.question_id
            WHERE ta.session_id = ?
            ORDER BY q.question_order ASC
        ", [$sessionId]);

        // Get correct options for each question
        $questionIds = array_map(fn($a) => $a->question_text, $answers);
        $questions = DB::select("
            SELECT q.id, q.question_text, q.expected_answer,
                   GROUP_CONCAT(CONCAT(qo.option_label, ':', qo.option_text, ':', IF(qo.is_correct, '1', '0')) ORDER BY qo.option_order SEPARATOR '||') as options_data
            FROM questions q
            LEFT JOIN question_options qo ON qo.question_id = q.id
            WHERE q.assessment_id = ?
            GROUP BY q.id, q.question_text, q.expected_answer
            ORDER BY q.question_order ASC
        ", [$assessment->id]);

        $questionMap = [];
        foreach ($questions as $q) {
            $opts = [];
            if ($q->options_data) {
                foreach (explode('||', $q->options_data) as $optStr) {
                    $parts = explode(':', $optStr, 3);
                    $opts[] = ['label' => $parts[0], 'text' => $parts[1], 'is_correct' => $parts[2] === '1'];
                }
            }
            $questionMap[$q->question_text] = ['options' => $opts, 'expected_answer' => $q->expected_answer];
        }

        $formattedAnswers = array_map(function ($a) use ($questionMap) {
            $qData = $questionMap[$a->question_text] ?? ['options' => [], 'expected_answer' => null];
            return [
                'question_text' => $a->question_text,
                'question_type' => $a->question_type,
                'max_points' => (float) $a->points,
                'points_earned' => (float) $a->points_earned,
                'is_correct' => (bool) $a->is_correct,
                'selected_options' => json_decode($a->selected_options ?? '[]'),
                'text_answer' => $a->text_answer,
                'options' => $qData['options'],
                'expected_answer' => $qData['expected_answer'],
            ];
        }, $answers);

        return response()->json([
            'session' => [
                'id' => $session->id,
                'candidate' => trim(($session->first_name ?? '') . ' ' . ($session->last_name ?? '')),
                'email' => $session->email,
                'score' => (float) $session->percentage,
                'passed' => (bool) $session->passed,
                'time_spent' => $session->time_spent_seconds,
                'tab_switches' => $session->tab_switches,
                'fullscreen_exits' => $session->fullscreen_exits,
            ],
            'answers' => $formattedAnswers,
            'total_questions' => count($formattedAnswers),
            'correct_count' => count(array_filter($formattedAnswers, fn($a) => $a['is_correct'])),
        ]);
    }

    protected function getScoreDistribution(string $assessmentId): array
    {
        $rows = DB::select("
            SELECT
                SUM(CASE WHEN percentage <= 20 THEN 1 ELSE 0 END) as r_0_20,
                SUM(CASE WHEN percentage > 20 AND percentage <= 40 THEN 1 ELSE 0 END) as r_21_40,
                SUM(CASE WHEN percentage > 40 AND percentage <= 60 THEN 1 ELSE 0 END) as r_41_60,
                SUM(CASE WHEN percentage > 60 AND percentage <= 80 THEN 1 ELSE 0 END) as r_61_80,
                SUM(CASE WHEN percentage > 80 THEN 1 ELSE 0 END) as r_81_100
            FROM test_sessions
            WHERE assessment_id = ? AND status IN ('submitted','timed_out')
        ", [$assessmentId]);

        $r = $rows[0] ?? null;
        return [
            ['label' => '0-20', 'value' => (int) ($r->r_0_20 ?? 0)],
            ['label' => '21-40', 'value' => (int) ($r->r_21_40 ?? 0)],
            ['label' => '41-60', 'value' => (int) ($r->r_41_60 ?? 0)],
            ['label' => '61-80', 'value' => (int) ($r->r_61_80 ?? 0)],
            ['label' => '81-100', 'value' => (int) ($r->r_81_100 ?? 0)],
        ];
    }
}
