<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Assessment Result - Quizly</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f9fafb;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 560px;">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <span style="font-size: 28px; font-weight: 700; color: #1a1a1a; letter-spacing: -0.5px;">Quizly.</span>
                        </td>
                    </tr>

                    <!-- Main Content Card -->
                    <tr>
                        <td>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <tr>
                                    <td style="padding: 48px 40px;">
                                        <p style="margin: 0 0 24px; color: #1a1a1a; font-size: 16px; line-height: 1.6;">
                                            Hi {{ $assessment->user->first_name ?? 'there' }},
                                        </p>

                                        <p style="margin: 0 0 24px; color: #374151; font-size: 15px; line-height: 1.6;">
                                            <strong>{{ $candidateName }}</strong> has completed the assessment <strong>"{{ $assessment->title }}"</strong>. Here are the results:
                                        </p>

                                        <!-- Score Card -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                            style="background-color: {{ $session->passed ? '#f0fdf4' : '#fef2f2' }}; border: 1px solid {{ $session->passed ? '#bbf7d0' : '#fecaca' }}; border-radius: 8px; margin-bottom: 24px;">
                                            <tr>
                                                <td style="padding: 24px; text-align: center;">
                                                    <p style="margin: 0 0 8px; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Score
                                                    </p>
                                                    <p style="margin: 0 0 8px; font-size: 36px; font-weight: 700; color: {{ $session->passed ? '#16a34a' : '#dc2626' }};">
                                                        {{ number_format($session->percentage ?? 0, 1) }}%
                                                    </p>
                                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: {{ $session->passed ? '#16a34a' : '#dc2626' }};">
                                                        {{ $session->passed ? '✓ Passed' : '✗ Did not pass' }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Details -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">Candidate</td>
                                                <td style="padding: 8px 0; color: #1a1a1a; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $candidateName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">Email</td>
                                                <td style="padding: 8px 0; color: #1a1a1a; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $invitee->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">Score</td>
                                                <td style="padding: 8px 0; color: #1a1a1a; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $session->total_score ?? 0 }} / {{ $session->max_score ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">Completed</td>
                                                <td style="padding: 8px 0; color: #1a1a1a; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{ $session->submitted_at?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                            </tr>
                                            @if($session->tab_switches > 0 || $session->fullscreen_exits > 0)
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px; border-bottom: 1px solid #f3f4f6;">Proctoring Flags</td>
                                                <td style="padding: 8px 0; color: #ef4444; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">
                                                    {{ $session->tab_switches }} tab switch(es), {{ $session->fullscreen_exits }} fullscreen exit(s)
                                                </td>
                                            </tr>
                                            @endif
                                        </table>

                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.5;">
                                            View the full results and breakdown on your <a href="{{ url('/assessments/' . $assessment->id) }}" style="color: #4f46e5; text-decoration: none;">dashboard</a>.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top: 32px;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                &copy; {{ date('Y') }} Quizly. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
