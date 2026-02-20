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
        table.data { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.data th { background: #f1f5f9; color: #374151; font-weight: 600; text-align: left; padding: 8px 10px; border: 1px solid #e5e7eb; }
        table.data td { padding: 7px 10px; border: 1px solid #e5e7eb; }
        .pass { color: #16a34a; font-weight: 600; }
        .fail { color: #dc2626; font-weight: 600; }
        .correct { color: #16a34a; }
        .wrong { color: #dc2626; }
        .score-box { text-align: center; padding: 24px; margin-bottom: 20px; border-radius: 8px; }
        .score-pass { background: #dcfce7; border: 2px solid #16a34a; }
        .score-fail { background: #fef2f2; border: 2px solid #dc2626; }
        .score-value { font-size: 36px; font-weight: 700; }
        .q-block { margin-bottom: 12px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 4px; page-break-inside: avoid; }
        .q-text { font-weight: 600; margin-bottom: 4px; }
        .q-meta { font-size: 10px; color: #6b7280; }
        .option { padding: 3px 8px; margin: 2px 0; font-size: 11px; }
        .option.selected { background: #eff6ff; border-left: 3px solid #3b82f6; }
        .option.correct-opt { background: #dcfce7; border-left: 3px solid #16a34a; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; padding: 20px; border-top: 1px solid #e5e7eb; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $assessment->title }}</h1>
        <p>Assessment Certificate — {{ $candidateName }}</p>
    </div>

    {{-- Result Summary --}}
    <div class="section">
        <div class="section-title">Your Result</div>
        <table class="data" style="margin-bottom:16px;">
            <tr>
                <th>Name</th><td>{{ $candidateName }}</td>
                <th>Email</th><td>{{ $invitee->email }}</td>
            </tr>
            <tr>
                <th>Submitted</th><td>{{ $session->submitted_at?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                <th>Time Spent</th><td>{{ $session->time_spent_seconds ? floor($session->time_spent_seconds / 60) . 'm ' . ($session->time_spent_seconds % 60) . 's' : 'N/A' }}</td>
            </tr>
        </table>

        @if($assessment->show_result_to_taker)
            <div class="score-box {{ $session->passed ? 'score-pass' : 'score-fail' }}">
                <div class="score-value" style="color:{{ $session->passed ? '#16a34a' : '#dc2626' }}">{{ number_format($session->percentage, 1) }}%</div>
                <div style="font-size:14px; color:{{ $session->passed ? '#16a34a' : '#dc2626' }}; font-weight:600;">
                    {{ $session->passed ? '✓ Passed' : '✗ Did not pass' }}
                </div>
                <div style="font-size:12px; color:#6b7280; margin-top:4px;">{{ $session->total_score }} / {{ $session->max_score }} points</div>
            </div>
        @else
            <div class="score-box" style="background:#f8fafc; border:2px solid #e5e7eb;">
                <div style="font-size:16px; font-weight:600; color:#374151;">Submission Recorded</div>
                <div style="font-size:12px; color:#6b7280; margin-top:4px;">The assessment creator will review your results.</div>
            </div>
        @endif
    </div>

    {{-- Questions Answered (only show if results visible) --}}
    @if($assessment->show_result_to_taker)
    <div class="section">
        <div class="section-title">Your Answers</div>
        @foreach($assessment->questions->sortBy('question_order') as $i => $question)
            @php
                $answer = $session->answers->firstWhere('question_id', $question->id);
            @endphp
            <div class="q-block">
                <div class="q-text">Q{{ $i + 1 }}. {{ $question->question_text }}</div>
                <div class="q-meta">{{ $question->points }} pt{{ $question->points != 1 ? 's' : '' }}
                    @if($answer)
                        — <span class="{{ $answer->is_correct ? 'correct' : 'wrong' }}">{{ $answer->is_correct ? '✓ Correct' : '✗ Incorrect' }} ({{ $answer->points_earned }}/{{ $question->points }})</span>
                    @else
                        — <span style="color:#9ca3af;">Not answered</span>
                    @endif
                </div>

                @if($question->options->isNotEmpty())
                    @foreach($question->options->sortBy('option_order') as $opt)
                        @php
                            $selected = $answer && is_array($answer->selected_options) && in_array($opt->option_label, $answer->selected_options);
                        @endphp
                        <div class="option {{ $selected ? 'selected' : '' }} {{ $opt->is_correct ? 'correct-opt' : '' }}">
                            {{ $opt->option_label }}. {{ $opt->option_text }}
                            @if($selected) <strong>[Your answer]</strong> @endif
                            @if($opt->is_correct) ✓ @endif
                        </div>
                    @endforeach
                @elseif($answer)
                    <div class="option selected">Your answer: {{ $answer->text_answer ?? '—' }}</div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        &copy; {{ date('Y') }} Quizly. This report was auto-generated.
    </div>
</body>
</html>
