<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Subscription Confirmed - Quizly</title>
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
                                        <!-- Greeting -->
                                        <p style="margin: 0 0 24px; color: #1a1a1a; font-size: 16px; line-height: 1.6;">
                                            Hi {{ $firstName }},
                                        </p>

                                        <p style="margin: 0 0 32px; color: #525252; font-size: 15px; line-height: 1.65;">
                                            Your <strong>{{ $planName }}</strong> subscription is now active. You have full access to all features.
                                        </p>

                                        <!-- Plan Details -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 32px;">
                                            <tr>
                                                <td style="padding: 24px;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                        <tr>
                                                            <td style="padding-bottom: 12px;">
                                                                <span style="color: #64748b; font-size: 13px;">Plan</span><br>
                                                                <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $planName }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding-bottom: 12px;">
                                                                <span style="color: #64748b; font-size: 13px;">Amount paid</span><br>
                                                                <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $currency }} {{ $amount }}</span>
                                                            </td>
                                                        </tr>
                                                        @if($expiresAt)
                                                        <tr>
                                                            <td>
                                                                <span style="color: #64748b; font-size: 13px;">Valid until</span><br>
                                                                <span style="color: #1e293b; font-size: 15px; font-weight: 600;">{{ $expiresAt }}</span>
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $dashboardUrl }}" style="display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; padding: 14px 32px; border-radius: 8px;">
                                                        Go to Dashboard
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <hr style="border: none; border-top: 1px solid #e4e4e7; margin: 32px 0;">

                                        <p style="margin: 0; color: #71717a; font-size: 13px; line-height: 1.5;">
                                            If you have any questions about your subscription, just reply to this email. We're happy to help.
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
