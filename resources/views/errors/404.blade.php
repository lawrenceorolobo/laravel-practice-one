<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .container {
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }
        .error-code {
            font-size: clamp(6rem, 20vw, 12rem);
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .title {
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        .description {
            font-size: clamp(0.875rem, 2vw, 1rem);
            color: #64748b;
            max-width: 400px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        /* Floating shapes */
        .shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
        }
        .shape-1 {
            width: 300px; height: 300px;
            background: #6366f1;
            top: -100px; right: -50px;
            animation: drift 8s ease-in-out infinite;
        }
        .shape-2 {
            width: 200px; height: 200px;
            background: #a855f7;
            bottom: -60px; left: -30px;
            animation: drift 6s ease-in-out infinite reverse;
        }
        .shape-3 {
            width: 150px; height: 150px;
            background: #8b5cf6;
            top: 40%; left: 10%;
            animation: drift 10s ease-in-out infinite;
        }
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(15px, -10px) scale(1.03); }
            66% { transform: translate(-10px, 8px) scale(0.97); }
        }
        .illustration {
            width: 200px;
            margin: 0 auto 1.5rem;
        }
        .illustration svg {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="container">
        <div class="illustration">
            <svg viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Magnifying glass -->
                <circle cx="90" cy="70" r="40" stroke="#6366f1" stroke-width="4" opacity="0.3"/>
                <circle cx="90" cy="70" r="30" stroke="#6366f1" stroke-width="3" opacity="0.15"/>
                <line x1="118" y1="98" x2="145" y2="125" stroke="#6366f1" stroke-width="6" stroke-linecap="round" opacity="0.3"/>
                <!-- Question mark -->
                <text x="80" y="82" font-family="Inter" font-size="36" font-weight="800" fill="#6366f1" opacity="0.6">?</text>
                <!-- Small dots -->
                <circle cx="40" cy="30" r="3" fill="#a855f7" opacity="0.3">
                    <animate attributeName="opacity" values="0.3;0.6;0.3" dur="2s" repeatCount="indefinite"/>
                </circle>
                <circle cx="150" cy="40" r="2" fill="#6366f1" opacity="0.4">
                    <animate attributeName="opacity" values="0.4;0.7;0.4" dur="3s" repeatCount="indefinite"/>
                </circle>
                <circle cx="160" cy="110" r="3" fill="#8b5cf6" opacity="0.3">
                    <animate attributeName="opacity" values="0.3;0.5;0.3" dur="2.5s" repeatCount="indefinite"/>
                </circle>
            </svg>
        </div>
        <div class="error-code">404</div>
        <h1 class="title">Page not found</h1>
        <p class="description">
            The page you're looking for doesn't exist or has been moved. Let's get you back on track.
        </p>
        <div class="actions">
            <a href="/" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Go Home
            </a>
            <button onclick="history.back()" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go Back
            </button>
        </div>
    </div>
</body>
</html>
