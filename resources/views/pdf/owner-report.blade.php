<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
        .header { background: #4f46e5; color: #fff; padding: 30px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; margin-bottom: 4px; }
        .header p { font-size: 13px; opacity: 0.85; }
        .section { padding: 0 30px; margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: 700; color: #4f46e5; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; }
        .stats-grid { display: table; width: 100%; margin-bottom: 16px; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; }
        .stat-value { font-size: 20px; font-weight: 700; color: #1a1a1a; }
        .stat-label { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        table.data { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.data th { background: #f1f5f9; color: #374151; font-weight: 600; text-align: left; padding: 8px 10px; border: 1px solid #e5e7eb; }
        table.data td { padding: 7px 10px; border: 1px solid #e5e7eb; }
        .pass { color: #16a34a; font-weight: 600; }
        .fail { color: #dc2626; font-weight: 600; }
        .correct { color: #16a34a; }
        .wrong { color: #dc2626; }
        .q-block { margin-bottom: 14px; page-break-inside: avoid; }
        .q-text { font-weight: 600; margin-bottom: 4px; }
        .q-meta { font-size: 10px; color: #6b7280; margin-bottom: 6px; }
        .option { padding: 3px 8px; margin: 2px 0; font-size: 11px; }
        .option.correct-opt { background: #dcfce7; border-left: 3px solid #16a34a; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; padding: 20px; border-top: 1px solid #e5e7eb; margin-top: 20px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $assessment->title }}</h1>
        <p>Assessment Report — Generated {{ now()->format('M j, Y g:i A') }}</p>
    </div>

    {{-- Overview Stats --}}
    <div class="section">
        <div class="section-title">Overview</div>
        <table style="width:100%;">
            <tr>
                <td class="stat-box">
                    <div class="stat-value">{{ $assessment->questions->count() }}</div>
                    <div class="stat-label">Questions</div>
                </td>
                <td class="stat-box">
                    <div class="stat-value">{{ $totalInvitees }}</div>
                    <div class="stat-label">Invitees</div>
                </td>
                <td class="stat-box">
                    <div class="stat-value">{{ $completedCount }}</div>
                    <div class="stat-label">Completed</div>
                </td>
                <td class="stat-box">
                    <div class="stat-value">{{ number_format($avgScore, 1) }}%</div>
                    <div class="stat-label">Avg Score</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Latest Candidate Result --}}
    <div class="section">
        <div class="section-title">Latest Result: {{ $candidateName }}</div>
        <table class="data">
            <tr>
                <th>Email</th><td>{{ $invitee->email }}</td>
                <th>Score</th><td class="{{ $session->passed ? 'pass' : 'fail' }}">{{ number_format($session->percentage, 1) }}% ({{ $session->total_score }}/{{ $session->max_score }})</td>
            </tr>
            <tr>
                <th>Status</th><td class="{{ $session->passed ? 'pass' : 'fail' }}">{{ $session->passed ? 'Passed' : 'Failed' }}</td>
                <th>Time Spent</th><td>{{ $session->time_spent_seconds ? floor($session->time_spent_seconds / 60) . 'm ' . ($session->time_spent_seconds % 60) . 's' : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Submitted</th><td>{{ $session->submitted_at?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                <th>Proctoring</th>
                <td>
                    @if($session->tab_switches > 0 || $session->fullscreen_exits > 0)
                        <span class="wrong">{{ $session->tab_switches }} tab switch(es), {{ $session->fullscreen_exits }} fullscreen exit(s)</span>
                    @else
                        <span class="correct">Clean — no flags</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Candidate's Answer Breakdown --}}
    <div class="section">
        <div class="section-title">Answer Breakdown</div>
        @foreach($assessment->questions->sortBy('question_order') as $i => $question)
            <div class="q-block">
                <div class="q-text">Q{{ $i + 1 }}. {{ $question->question_text }}</div>
                <div class="q-meta">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} · {{ $question->points }} pt{{ $question->points != 1 ? 's' : '' }}</div>

                @php
                    $answer = $session->answers->firstWhere('question_id', $question->id);
                @endphp

                @if($question->options->isNotEmpty())
                    @foreach($question->options->sortBy('option_order') as $opt)
                        @php
                            $selected = $answer && is_array($answer->selected_options) && in_array($opt->option_label, $answer->selected_options);
                        @endphp
                        <div class="option {{ $opt->is_correct ? 'correct-opt' : '' }}">
                            {{ $opt->option_label }}. {{ $opt->option_text }}
                            @if($selected) <strong>[Selected]</strong> @endif
                            @if($opt->is_correct) ✓ @endif
                        </div>
                    @endforeach
                @elseif($question->expected_answer)
                    <div class="option">Expected: {{ $question->expected_answer }}</div>
                    <div class="option {{ $answer && $answer->is_correct ? 'correct-opt' : '' }}">
                        Answered: {{ $answer->text_answer ?? '—' }}
                        @if($answer) <strong class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">[{{ $answer->is_correct ? 'Correct' : 'Incorrect' }}]</strong> @endif
                    </div>
                @endif

                @if($answer)
                    <div style="font-size:10px; margin-top:2px;">
                        Result: <span class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">{{ $answer->is_correct ? '✓ Correct' : '✗ Incorrect' }}</span>
                        — {{ $answer->points_earned }}/{{ $question->points }} pts
                    </div>
                @else
                    <div style="font-size:10px; margin-top:2px; color:#9ca3af;">Not answered</div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Quizly. This report was auto-generated. Do not share without authorization.
    </div>
</body>
</html>
