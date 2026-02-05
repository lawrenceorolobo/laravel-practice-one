<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Invitation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1a1a2e;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #64748b;
            font-size: 14px;
        }
        .detail-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .calendar-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        .calendar-title {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 12px;
        }
        .calendar-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .calendar-link {
            display: inline-block;
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #4f46e5;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        .calendar-link:hover {
            background: #f1f5f9;
        }
        .footer {
            background: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $assessment->title }}</h1>
            <p>You've been invited to take an assessment</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $invitee->first_name ?? 'there' }},</p>
            
            <p>You have been invited to complete an assessment. Please review the details below and click the button to begin when you're ready.</p>
            
            <div class="details-box">
                <div class="detail-row">
                    <span class="detail-label">Assessment</span>
                    <span class="detail-value">{{ $assessment->title }}</span>
                </div>
                @if($assessment->duration_minutes)
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">{{ $assessment->duration_minutes }} minutes</span>
                </div>
                @endif
                @if($assessment->start_datetime)
                <div class="detail-row">
                    <span class="detail-label">Start Date</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($assessment->start_datetime)->format('M d, Y \a\t g:i A') }}</span>
                </div>
                @endif
                @if($assessment->end_datetime)
                <div class="detail-row">
                    <span class="detail-label">Deadline</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($assessment->end_datetime)->format('M d, Y \a\t g:i A') }}</span>
                </div>
                @endif
            </div>
            
            <p style="text-align: center;">
                <a href="{{ $testUrl }}" class="cta-button">Start Assessment</a>
            </p>
            
            <div class="calendar-section">
                <p class="calendar-title">Add to Your Calendar</p>
                <div class="calendar-links">
                    <a href="{{ $calendarLinks['google'] }}" target="_blank" class="calendar-link">📅 Google Calendar</a>
                    <a href="{{ $calendarLinks['outlook'] }}" target="_blank" class="calendar-link">📧 Outlook</a>
                </div>
            </div>
            
            <p style="margin-top: 24px; font-size: 14px; color: #64748b;">
                <strong>Your unique access link:</strong><br>
                <a href="{{ $testUrl }}" style="color: #4f46e5; word-break: break-all;">{{ $testUrl }}</a>
            </p>
        </div>
        
        <div class="footer">
            <p>This invitation was sent via <a href="{{ url('/') }}">Quizly</a></p>
            <p>If you did not expect this email, please ignore it.</p>
        </div>
    </div>
</body>
</html>
