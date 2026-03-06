<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:40px 20px;">
<tr><td align="center">
<table width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
    <!-- Header -->
    <tr><td style="background:#6366f1;padding:32px 40px;text-align:center;">
        <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">New Contact Message</h1>
    </td></tr>
    <!-- Body -->
    <tr><td style="padding:32px 40px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr><td style="padding:12px 16px;background:#f1f5f9;border-radius:8px;margin-bottom:8px;">
                <p style="margin:0;font-size:12px;color:#64748b;font-weight:600;">FROM</p>
                <p style="margin:4px 0 0;font-size:15px;color:#1e293b;">{{ $senderName }}</p>
            </td></tr>
            <tr><td style="height:8px;"></td></tr>
            <tr><td style="padding:12px 16px;background:#f1f5f9;border-radius:8px;">
                <p style="margin:0;font-size:12px;color:#64748b;font-weight:600;">EMAIL</p>
                <p style="margin:4px 0 0;font-size:15px;color:#6366f1;"><a href="mailto:{{ $senderEmail }}" style="color:#6366f1;text-decoration:none;">{{ $senderEmail }}</a></p>
            </td></tr>
            <tr><td style="height:8px;"></td></tr>
            <tr><td style="padding:12px 16px;background:#f1f5f9;border-radius:8px;">
                <p style="margin:0;font-size:12px;color:#64748b;font-weight:600;">SUBJECT</p>
                <p style="margin:4px 0 0;font-size:15px;color:#1e293b;">{{ $contactSubject }}</p>
            </td></tr>
        </table>
        <div style="border-top:1px solid #e2e8f0;padding-top:20px;">
            <p style="font-size:12px;color:#64748b;font-weight:600;margin:0 0 8px;">MESSAGE</p>
            <p style="font-size:15px;color:#334155;line-height:1.7;margin:0;white-space:pre-wrap;">{{ $body }}</p>
        </div>
    </td></tr>
    <!-- Footer -->
    <tr><td style="padding:20px 40px;background:#f8fafc;text-align:center;border-top:1px solid #e2e8f0;">
        <p style="margin:0;font-size:12px;color:#94a3b8;">Sent via Quizly Contact Form</p>
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
