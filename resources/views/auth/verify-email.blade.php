<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 flex items-center justify-center p-6">

    <div class="w-full max-w-md text-center">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="text-4xl font-black bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
            Quizly.
        </a>
        
        <!-- Card -->
        <div class="mt-8 bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            <!-- Icon -->
            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-4">Verify Your Email</h1>
            <p class="text-indigo-200/70 mb-8">
                We've sent a verification link to your email address. Please check your inbox and click the link to verify your account.
            </p>
            
            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-xl text-sm">
                    {{ session('message') }}
                </div>
            @endif
            
            <!-- Resend Form -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                    Resend Verification Email
                </button>
            </form>
            
            <p class="text-indigo-200/50 text-sm mt-6">
                Didn't receive the email? Check your spam folder.
            </p>
        </div>
        
        <!-- Back Link -->
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white mt-8 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to home
        </a>
    </div>

</body>
</html>
