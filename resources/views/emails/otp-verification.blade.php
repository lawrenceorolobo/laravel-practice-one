<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your Email - Quizly</title>
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
                                            Hi {{ $firstName }},
                                        </p>
                                        
                                        <!-- Main Message -->
                                        <p style="margin: 0 0 32px; color: #525252; font-size: 15px; line-height: 1.65;">
                                            Enter this verification code to complete your email verification:
                                        </p>
                                        
                                        <!-- OTP Code Box -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center" style="padding: 24px 0 32px;">
                                                    <div style="display: inline-block; background-color: #f4f4f5; border-radius: 8px; padding: 20px 32px; border: 1px solid #e4e4e7;">
                                                        <span style="font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace; font-size: 32px; font-weight: 600; letter-spacing: 8px; color: #18181b;">
                                                            {{ $otp }}
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Expiry Notice -->
                                        <p style="margin: 0 0 24px; color: #71717a; font-size: 14px; line-height: 1.5; text-align: center;">
                                            This code expires in <strong style="color: #525252;">10 minutes</strong>
                                        </p>
                                        
                                        <!-- Divider -->
                                        <hr style="border: none; border-top: 1px solid #e4e4e7; margin: 32px 0;">
                                        
                                        <!-- Security Notice -->
                                        <p style="margin: 0; color: #71717a; font-size: 13px; line-height: 1.5;">
                                            If you didn't request this code, you can safely ignore this email. Someone may have entered your email address by mistake.
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
