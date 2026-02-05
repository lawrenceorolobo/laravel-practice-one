<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <!-- Back Button -->
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white mb-8 transition-colors group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to login
        </a>

        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-4xl font-black bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                Quizly.
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            <h2 class="text-2xl font-bold text-white mb-2">Forgot your password?</h2>
            <p class="text-indigo-200/70 mb-6">Enter your email and we'll send you a reset link.</p>

            <!-- Messages -->
            <div id="successDiv" class="hidden mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-xl text-sm"></div>
            <div id="errorDiv" class="hidden mb-6 p-4 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-sm"></div>

            <form id="forgotForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Email address</label>
                    <input type="email" id="email" required
                        class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="you@company.com">
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all disabled:opacity-50">
                    <span id="btnText">Send Reset Link</span>
                    <svg id="btnSpinner" class="hidden w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('forgotForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorDiv = document.getElementById('errorDiv');
        const successDiv = document.getElementById('successDiv');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            try {
                const res = await fetch('/api/auth/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: document.getElementById('email').value })
                });

                const result = await res.json();
                successDiv.textContent = result.message || 'If an account exists, you will receive an email.';
                successDiv.classList.remove('hidden');
            } catch (err) {
                errorDiv.textContent = 'Something went wrong. Please try again.';
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
