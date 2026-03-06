<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics — optimized with cached assessment IDs
     */
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $weekAgo = Carbon::now()->subDays(7)->toDateTimeString();
        $monthAgo = Carbon::now()->subDays(30)->toDateTimeString();
        $twoMonthsAgo = Carbon::now()->subDays(60)->toDateTimeString();

        $assessmentIds = Assessment::where('user_id', $userId)->pluck('id')->all();

        if (empty($assessmentIds)) {
            return response()->json([
                'total_assessments' => 0, 'total_candidates' => 0,
                'completion_rate' => 0, 'avg_score' => 0,
                'assessments_this_week' => 0, 'candidates_this_week' => 0,
                'completion_change' => 0,
            ]);
        }

        $ph = implode(',', array_fill(0, count($assessmentIds), '?'));

        $stats = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM assessments WHERE user_id = ?) as total_assessments,
                (SELECT COUNT(*) FROM assessments WHERE user_id = ? AND created_at >= ?) as assessments_this_week,
                (SELECT COUNT(DISTINCT email) FROM invitees WHERE assessment_id IN ({$ph})) as total_candidates,
                (SELECT COUNT(DISTINCT email) FROM invitees WHERE assessment_id IN ({$ph}) AND created_at >= ?) as candidates_this_week,
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out')) as total_completed,
                (SELECT ROUND(AVG(percentage)) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out') AND percentage IS NOT NULL) as avg_score
        ", array_merge(
            [$userId, $userId, $weekAgo],
            $assessmentIds, $assessmentIds, [$weekAgo],
            $assessmentIds, $assessmentIds
        ));

        $rates = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out') AND created_at >= ?) as lm_completed,
                (SELECT COUNT(*) FROM invitees WHERE assessment_id IN ({$ph}) AND created_at >= ?) as lm_candidates,
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out') AND created_at BETWEEN ? AND ?) as pm_completed,
                (SELECT COUNT(*) FROM invitees WHERE assessment_id IN ({$ph}) AND created_at BETWEEN ? AND ?) as pm_candidates
        ", array_merge(
            $assessmentIds, [$monthAgo],
            $assessmentIds, [$monthAgo],
            $assessmentIds, [$twoMonthsAgo, $monthAgo],
            $assessmentIds, [$twoMonthsAgo, $monthAgo]
        ));

        $totalCandidates = (int) $stats->total_candidates;
        $totalCompleted = (int) $stats->total_completed;
        $completionRate = $totalCandidates > 0 ? round(($totalCompleted / $totalCandidates) * 100) : 0;

        $lmRate = $rates->lm_candidates > 0 ? ($rates->lm_completed / $rates->lm_candidates) * 100 : 0;
        $pmRate = $rates->pm_candidates > 0 ? ($rates->pm_completed / $rates->pm_candidates) * 100 : 0;

        return response()->json([
            'total_assessments' => (int) $stats->total_assessments,
            'total_candidates' => $totalCandidates,
            'completion_rate' => $completionRate,
            'avg_score' => (int) ($stats->avg_score ?? 0),
            'assessments_this_week' => (int) $stats->assessments_this_week,
            'candidates_this_week' => (int) $stats->candidates_this_week,
            'completion_change' => round($lmRate - $pmRate),
        ]);
    }

    /**
     * Recent activity — JOINs only
     */
    public function activity(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $rows = DB::select("
            (SELECT 'test_completed' as type, 'emerald' as color,
                CONCAT(COALESCE(i.first_name,'A candidate'), ' completed \"', COALESCE(a.title,'an assessment'), '\" with ', ROUND(COALESCE(ts.percentage,0)), '%% score') as message,
                ts.created_at
            FROM test_sessions ts
            JOIN invitees i ON i.id = ts.invitee_id
            JOIN assessments a ON a.id = ts.assessment_id
            WHERE a.user_id = ? AND ts.status IN ('submitted','completed','timed_out')
            ORDER BY ts.created_at DESC LIMIT 5)
            UNION ALL
            (SELECT 'invitee_added' as type, 'indigo' as color,
                CONCAT(COALESCE(inv.first_name,'A candidate'), ' was invited to \"', COALESCE(a2.title,'an assessment'), '\"') as message,
                inv.created_at
            FROM invitees inv
            JOIN assessments a2 ON a2.id = inv.assessment_id
            WHERE a2.user_id = ?
            ORDER BY inv.created_at DESC LIMIT 3)
            ORDER BY created_at DESC LIMIT 5
        ", [$userId, $userId]);

        $icons = [
            'test_completed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'invitee_added' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        ];

        $activities = array_map(fn($r) => [
            'type' => $r->type,
            'color' => $r->color,
            'icon' => $icons[$r->type] ?? '',
            'message' => $r->message,
            'time_ago' => Carbon::parse($r->created_at)->diffForHumans(),
        ], $rows);

        return response()->json(['data' => $activities]);
    }

    /**
     * Full analytics — comprehensive data for premium analytics page
     */
    public function analytics(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay()->toDateTimeString();
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay()->toDateTimeString();

        $assessmentIds = Assessment::where('user_id', $userId)->pluck('id')->all();

        if (empty($assessmentIds)) {
            $empty7 = [];
            for ($i = 6; $i >= 0; $i--) $empty7[] = ['day' => Carbon::now()->subDays($i)->format('D'), 'count' => 0];
            return response()->json([
                'total_tests' => 0, 'avg_score' => 0, 'pass_rate' => 0, 'avg_time_minutes' => 0,
                'total_candidates' => 0, 'completion_rate' => 0,
                'score_distribution' => ['0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0],
                'tests_over_time' => $empty7,
                'completion_funnel' => ['invited' => 0, 'started' => 0, 'completed' => 0, 'passed' => 0],
                'time_distribution' => ['0-2' => 0, '2-5' => 0, '5-10' => 0, '10-20' => 0, '20+' => 0],
                'monthly_trend' => [],
                'top_scorers' => [],
                'question_difficulty' => [],
            ]);
        }

        $ph = implode(',', array_fill(0, count($assessmentIds), '?'));

        // 1. Core aggregates
        $agg = DB::selectOne("
            SELECT
                COUNT(*) as total_tests,
                ROUND(AVG(percentage)) as avg_score,
                ROUND(AVG(time_spent_seconds)) as avg_time,
                SUM(CASE WHEN percentage >= 50 THEN 1 ELSE 0 END) as pass_count,
                SUM(CASE WHEN percentage <= 20 THEN 1 ELSE 0 END) as s0_20,
                SUM(CASE WHEN percentage > 20 AND percentage <= 40 THEN 1 ELSE 0 END) as s21_40,
                SUM(CASE WHEN percentage > 40 AND percentage <= 60 THEN 1 ELSE 0 END) as s41_60,
                SUM(CASE WHEN percentage > 60 AND percentage <= 80 THEN 1 ELSE 0 END) as s61_80,
                SUM(CASE WHEN percentage > 80 THEN 1 ELSE 0 END) as s81_100,
                SUM(CASE WHEN time_spent_seconds <= 120 THEN 1 ELSE 0 END) as t0_2,
                SUM(CASE WHEN time_spent_seconds > 120 AND time_spent_seconds <= 300 THEN 1 ELSE 0 END) as t2_5,
                SUM(CASE WHEN time_spent_seconds > 300 AND time_spent_seconds <= 600 THEN 1 ELSE 0 END) as t5_10,
                SUM(CASE WHEN time_spent_seconds > 600 AND time_spent_seconds <= 1200 THEN 1 ELSE 0 END) as t10_20,
                SUM(CASE WHEN time_spent_seconds > 1200 THEN 1 ELSE 0 END) as t20plus
            FROM test_sessions
            WHERE assessment_id IN ({$ph})
              AND status IN ('submitted','completed','timed_out')
              AND percentage IS NOT NULL
        ", $assessmentIds);

        // 2. Tests over time (7 days)
        $dailyRows = DB::select("
            SELECT DATE(created_at) as d, COUNT(*) as cnt
            FROM test_sessions
            WHERE assessment_id IN ({$ph})
              AND status IN ('submitted','completed','timed_out')
              AND created_at >= ?
            GROUP BY DATE(created_at)
        ", array_merge($assessmentIds, [$sevenDaysAgo]));

        $dailyMap = collect($dailyRows)->pluck('cnt', 'd');
        $testsOverTime = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $testsOverTime[] = ['day' => $date->format('D'), 'count' => (int) ($dailyMap[$date->toDateString()] ?? 0)];
        }

        // 3. Completion funnel
        $funnel = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM invitees WHERE assessment_id IN ({$ph})) as invited,
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status != 'in_progress') as started,
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out')) as completed,
                (SELECT COUNT(*) FROM test_sessions WHERE assessment_id IN ({$ph}) AND status IN ('submitted','completed','timed_out') AND passed = 1) as passed
        ", array_merge($assessmentIds, $assessmentIds, $assessmentIds, $assessmentIds));

        // 4. Monthly trend (last 30 days)
        $monthlyRows = DB::select("
            SELECT DATE(created_at) as d, COUNT(*) as tests, ROUND(AVG(percentage)) as avg
            FROM test_sessions
            WHERE assessment_id IN ({$ph})
              AND status IN ('submitted','completed','timed_out')
              AND percentage IS NOT NULL
              AND created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY d ASC
        ", array_merge($assessmentIds, [$thirtyDaysAgo]));

        // 5. Top scorers
        $topScorers = DB::select("
            SELECT first_name, last_name, email, percentage, time_spent_seconds
            FROM test_sessions
            WHERE assessment_id IN ({$ph})
              AND status IN ('submitted','completed','timed_out')
              AND percentage IS NOT NULL
            ORDER BY percentage DESC, time_spent_seconds ASC
            LIMIT 5
        ", $assessmentIds);

        // 6. Question difficulty analysis (hardest questions across all assessments)
        $questionDiff = DB::select("
            SELECT q.question_text,
                   COUNT(ta.id) as attempts,
                   SUM(CASE WHEN ta.is_correct = 1 THEN 1 ELSE 0 END) as correct,
                   ROUND(SUM(CASE WHEN ta.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(ta.id) * 100) as success_rate
            FROM test_answers ta
            JOIN questions q ON q.id = ta.question_id
            WHERE q.assessment_id IN ({$ph})
            GROUP BY q.id, q.question_text
            HAVING attempts >= 2
            ORDER BY success_rate ASC
            LIMIT 10
        ", $assessmentIds);

        $total = (int) ($agg->total_tests ?? 0);
        $passRate = $total > 0 ? round(((int) ($agg->pass_count ?? 0) / $total) * 100) : 0;
        $totalCandidates = (int) ($funnel->invited ?? 0);
        $completionRate = $totalCandidates > 0 ? round(((int) ($funnel->completed ?? 0) / $totalCandidates) * 100) : 0;

        return response()->json([
            'total_tests' => $total,
            'avg_score' => (int) ($agg->avg_score ?? 0),
            'pass_rate' => $passRate,
            'avg_time_minutes' => $agg->avg_time ? round($agg->avg_time / 60) : 0,
            'total_candidates' => $totalCandidates,
            'completion_rate' => $completionRate,
            'score_distribution' => [
                '0-20' => (int) ($agg->s0_20 ?? 0),
                '21-40' => (int) ($agg->s21_40 ?? 0),
                '41-60' => (int) ($agg->s41_60 ?? 0),
                '61-80' => (int) ($agg->s61_80 ?? 0),
                '81-100' => (int) ($agg->s81_100 ?? 0),
            ],
            'tests_over_time' => $testsOverTime,
            'completion_funnel' => [
                'invited' => (int) ($funnel->invited ?? 0),
                'started' => (int) ($funnel->started ?? 0),
                'completed' => (int) ($funnel->completed ?? 0),
                'passed' => (int) ($funnel->passed ?? 0),
            ],
            'time_distribution' => [
                '0-2 min' => (int) ($agg->t0_2 ?? 0),
                '2-5 min' => (int) ($agg->t2_5 ?? 0),
                '5-10 min' => (int) ($agg->t5_10 ?? 0),
                '10-20 min' => (int) ($agg->t10_20 ?? 0),
                '20+ min' => (int) ($agg->t20plus ?? 0),
            ],
            'monthly_trend' => array_map(fn($r) => [
                'date' => $r->d,
                'tests' => (int) $r->tests,
                'avg_score' => (int) $r->avg,
            ], $monthlyRows),
            'top_scorers' => array_map(fn($r) => [
                'name' => trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')),
                'email' => $r->email,
                'score' => (float) $r->percentage,
                'time_minutes' => $r->time_spent_seconds ? round($r->time_spent_seconds / 60, 1) : null,
            ], $topScorers),
            'question_difficulty' => array_map(fn($r) => [
                'question' => strlen($r->question_text) > 60 ? substr($r->question_text, 0, 57) . '...' : $r->question_text,
                'attempts' => (int) $r->attempts,
                'correct' => (int) $r->correct,
                'success_rate' => (int) $r->success_rate,
            ], $questionDiff),
        ]);
    }
}
