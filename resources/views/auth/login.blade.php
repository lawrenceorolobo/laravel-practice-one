<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-6">

    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Back Button -->
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white mb-8 transition-colors group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to home
        </a>

        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-4xl font-black text-indigo-400">
                Quizly.
            </a>
            <p class="text-indigo-200 mt-2">Enterprise Assessment Platform</p>
        </div>

        <!-- Card -->
        <div class="glass-card rounded-3xl p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Sign in to your account</h2>

            <!-- Error Message -->
            <div id="errorDiv" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm"></div>

            <form id="loginForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email address</label>
                    <input type="email" id="email" required
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="you@company.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input type="password" id="password" required
                        class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Forgot password?</a>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                    <span id="btnText">Sign in</span>
                    <svg id="btnSpinner" class="hidden w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </form>

            <p class="text-center text-slate-600 mt-8">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Create account</a>
            </p>
        </div>

        <!-- Trust -->
        <p class="text-center text-indigo-200/50 text-sm mt-8">
            Protected by enterprise-grade security
        </p>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorDiv = document.getElementById('errorDiv');

        // Sanitize error messages - never show SQL or technical errors
        function sanitizeErrorMessage(message) {
            if (!message || typeof message !== 'string') {
                return 'Something went wrong. Give it another try.';
            }
            const technicalPatterns = [
                'SQLSTATE', 'SQL:', 'Query', 'Connection refused', 
                'Base table', 'doesn\'t exist', 'PDO', 'mysql',
                'ECONNREFUSED', 'timeout', 'Exception'
            ];
            for (const pattern of technicalPatterns) {
                if (message.toLowerCase().includes(pattern.toLowerCase())) {
                    return 'We\'re having some trouble right now. Try again in a bit.';
                }
            }
            return message;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Loading state
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            errorDiv.classList.add('hidden');

            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                remember: document.getElementById('remember').checked
            };

            try {
                const res = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    localStorage.setItem('token', result.token);
                    localStorage.setItem('user', JSON.stringify(result.user));
                    // Redirect based on subscription status
                    const redirectUrl = result.user.has_active_subscription ? '/dashboard' : '/select-plan';
                    window.location.href = redirectUrl;
                } else {
                    throw new Error(sanitizeErrorMessage(result.message || 'Wrong email or password.'));
                }
            } catch (err) {
                errorDiv.textContent = sanitizeErrorMessage(err.message);
                errorDiv.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
