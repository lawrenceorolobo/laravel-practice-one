<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Assessment Invitation - Quizly</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f9fafb;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Container -->
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
                                        <!-- Greeting -->
                                        <p style="margin: 0 0 24px; color: #1a1a1a; font-size: 16px; line-height: 1.6;">
                                            Hi {{ $invitee->first_name ?? $invitee->email }},
                                        </p>

                                        <!-- Main Message -->
                                        <p style="margin: 0 0 28px; color: #525252; font-size: 15px; line-height: 1.65;">
                                            You've been invited to take the <strong style="color: #1a1a1a;">{{ $assessment->title }}</strong> assessment. Review the details below and click the button when you're ready to begin.
                                        </p>

                                        <!-- Details Box -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f5; border-radius: 8px; border: 1px solid #e4e4e7; margin-bottom: 32px;">
                                            @if($assessment->duration_minutes)
                                            <tr>
                                                <td style="padding: 12px 20px; border-bottom: 1px solid #e4e4e7; color: #71717a; font-size: 14px; width: 40%;">Duration</td>
                                                <td style="padding: 12px 20px; border-bottom: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600;">{{ $assessment->duration_minutes }} minutes</td>
                                            </tr>
                                            @endif
                                            @if($assessment->start_datetime)
                                            <tr>
                                                <td style="padding: 12px 20px; border-bottom: 1px solid #e4e4e7; color: #71717a; font-size: 14px;">Start Date</td>
                                                <td style="padding: 12px 20px; border-bottom: 1px solid #e4e4e7; color: #18181b; font-size: 14px; font-weight: 600;">{{ \Carbon\Carbon::parse($assessment->start_datetime)->format('M d, Y \a\t g:i A') }}</td>
                                            </tr>
                                            @endif
                                            @if($assessment->end_datetime)
                                            <tr>
                                                <td style="padding: 12px 20px; color: #71717a; font-size: 14px;">Deadline</td>
                                                <td style="padding: 12px 20px; color: #18181b; font-size: 14px; font-weight: 600;">{{ \Carbon\Carbon::parse($assessment->end_datetime)->format('M d, Y \a\t g:i A') }}</td>
                                            </tr>
                                            @endif
                                        </table>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center" style="padding: 0 0 28px;">
                                                    <a href="{{ $testUrl }}" style="display: inline-block; background-color: #18181b; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px;">
                                                        Start Assessment →
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Calendar Links -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 20px 0 0; border-top: 1px solid #e4e4e7;">
                                                    <p style="margin: 0 0 12px; color: #71717a; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Add to Calendar</p>
                                                    <a href="{{ $calendarLinks['google'] }}" target="_blank" style="display: inline-block; padding: 8px 16px; border: 1px solid #e4e4e7; border-radius: 6px; color: #525252; text-decoration: none; font-size: 13px; font-weight: 500; margin-right: 8px;">📅 Google</a>
                                                    <a href="{{ $calendarLinks['outlook'] }}" target="_blank" style="display: inline-block; padding: 8px 16px; border: 1px solid #e4e4e7; border-radius: 6px; color: #525252; text-decoration: none; font-size: 13px; font-weight: 500;">📧 Outlook</a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Divider -->
                                        <hr style="border: none; border-top: 1px solid #e4e4e7; margin: 24px 0;">

                                        <!-- Security Notice -->
                                        <p style="margin: 0; color: #a1a1aa; font-size: 13px; line-height: 1.5;">
                                            This is your unique assessment link — do not share it. If you did not expect this invitation, please ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 32px 20px; text-align: center;">
                            <p style="margin: 0 0 8px; color: #a1a1aa; font-size: 13px;">
                                Quizly &middot; Assessment Platform
                            </p>
                            <p style="margin: 0; color: #d4d4d8; font-size: 12px;">
                                © {{ date('Y') }} Quizly. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
