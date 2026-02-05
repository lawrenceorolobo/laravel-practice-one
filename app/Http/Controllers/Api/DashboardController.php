<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Invitee;
use App\Models\TestSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for authenticated user
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get all user's assessments
        $assessments = Assessment::where('user_id', $user->id)->get();
        $assessmentIds = $assessments->pluck('id');
        
        // Calculate total stats
        $totalAssessments = $assessments->count();
        
        // Get invitees count for all assessments
        $totalCandidates = Invitee::whereIn('assessment_id', $assessmentIds)->count();
        
        // Get completed sessions count
        $totalCompleted = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->count();
        
        // Calculate completion rate
        $completionRate = $totalCandidates > 0 
            ? round(($totalCompleted / $totalCandidates) * 100) 
            : 0;
        
        // Calculate average score
        $avgScore = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->whereNotNull('score')
            ->avg('score');
        
        $avgScore = $avgScore ? round($avgScore) : 0;
        
        // Calculate this week's changes
        $weekAgo = Carbon::now()->subDays(7);
        
        $assessmentsThisWeek = Assessment::where('user_id', $user->id)
            ->where('created_at', '>=', $weekAgo)
            ->count();
        
        $candidatesThisWeek = Invitee::whereIn('assessment_id', $assessmentIds)
            ->where('created_at', '>=', $weekAgo)
            ->count();
        
        // Calculate month change for completion rate
        $monthAgo = Carbon::now()->subDays(30);
        $twoMonthsAgo = Carbon::now()->subDays(60);
        
        $lastMonthCompleted = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthAgo)
            ->count();
        
        $lastMonthCandidates = Invitee::whereIn('assessment_id', $assessmentIds)
            ->where('created_at', '>=', $monthAgo)
            ->count();
        
        $prevMonthCompleted = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        
        $prevMonthCandidates = Invitee::whereIn('assessment_id', $assessmentIds)
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->count();
        
        $lastMonthRate = $lastMonthCandidates > 0 ? ($lastMonthCompleted / $lastMonthCandidates) * 100 : 0;
        $prevMonthRate = $prevMonthCandidates > 0 ? ($prevMonthCompleted / $prevMonthCandidates) * 100 : 0;
        $completionChange = round($lastMonthRate - $prevMonthRate);
        
        return response()->json([
            'total_assessments' => $totalAssessments,
            'total_candidates' => $totalCandidates,
            'completion_rate' => $completionRate,
            'avg_score' => $avgScore,
            'assessments_this_week' => $assessmentsThisWeek,
            'candidates_this_week' => $candidatesThisWeek,
            'completion_change' => $completionChange,
        ]);
    }
    
    /**
     * Get recent activity for authenticated user
     */
    public function activity(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Get user's assessments
        $assessmentIds = Assessment::where('user_id', $user->id)->pluck('id');
        
        $activities = [];
        
        // Recent test completions
        $recentSessions = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->with(['invitee', 'assessment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentSessions as $session) {
            $activities[] = [
                'type' => 'test_completed',
                'color' => 'emerald',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'message' => ($session->invitee?->first_name ?? 'A candidate') . 
                    ' completed "' . ($session->assessment?->title ?? 'an assessment') . 
                    '" with ' . ($session->score ?? 0) . '% score',
                'time_ago' => $session->created_at->diffForHumans(),
            ];
        }
        
        // Recent invitees added
        $recentInvitees = Invitee::whereIn('assessment_id', $assessmentIds)
            ->with('assessment')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        foreach ($recentInvitees as $invitee) {
            $activities[] = [
                'type' => 'invitee_added',
                'color' => 'indigo',
                'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                'message' => ($invitee->first_name ?? 'A candidate') . ' was invited to "' . 
                    ($invitee->assessment?->title ?? 'an assessment') . '"',
                'time_ago' => $invitee->created_at->diffForHumans(),
            ];
        }
        
        // Sort by most recent first
        usort($activities, function($a, $b) {
            return strtotime($b['time_ago']) <=> strtotime($a['time_ago']);
        });
        
        return response()->json([
            'data' => array_slice($activities, 0, 5),
        ]);
    }

    /**
     * Get analytics data for charts
     */
    public function analytics(Request $request): JsonResponse
    {
        $user = $request->user();
        $assessmentIds = Assessment::where('user_id', $user->id)->pluck('id');
        
        // Score Distribution (0-20, 21-40, 41-60, 61-80, 81-100)
        $scoreDistribution = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];
        
        $sessions = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->whereNotNull('score_percentage')
            ->get(['score_percentage']);
        
        foreach ($sessions as $session) {
            $score = $session->score_percentage;
            if ($score <= 20) $scoreDistribution['0-20']++;
            elseif ($score <= 40) $scoreDistribution['21-40']++;
            elseif ($score <= 60) $scoreDistribution['41-60']++;
            elseif ($score <= 80) $scoreDistribution['61-80']++;
            else $scoreDistribution['81-100']++;
        }
        
        // Tests Over Time (last 7 days)
        $testsOverTime = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayName = $date->format('D');
            $count = TestSession::whereIn('assessment_id', $assessmentIds)
                ->where('status', 'completed')
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $testsOverTime[] = ['day' => $dayName, 'count' => $count];
        }
        
        // Calculate totals
        $totalTests = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->count();
            
        $avgScore = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->whereNotNull('score_percentage')
            ->avg('score_percentage');
            
        $avgTime = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->whereNotNull('time_taken')
            ->avg('time_taken');
        
        $passCount = TestSession::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'completed')
            ->where('score_percentage', '>=', 50)
            ->count();
        
        $passRate = $totalTests > 0 ? round(($passCount / $totalTests) * 100) : 0;
        
        return response()->json([
            'total_tests' => $totalTests,
            'avg_score' => $avgScore ? round($avgScore) : 0,
            'pass_rate' => $passRate,
            'avg_time_minutes' => $avgTime ? round($avgTime / 60) : 0,
            'score_distribution' => $scoreDistribution,
            'tests_over_time' => $testsOverTime,
        ]);
    }
}
