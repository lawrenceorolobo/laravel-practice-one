<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="text-4xl font-black bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                Quizly.
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            <h2 class="text-2xl font-bold text-white mb-6">Reset Your Password</h2>

            <!-- Messages -->
            <div id="successDiv" class="hidden mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-xl text-sm"></div>
            <div id="errorDiv" class="hidden mb-6 p-4 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-sm"></div>

            <form id="resetForm" class="space-y-5">
                <input type="hidden" id="token" value="{{ $token ?? request()->route('token') }}">
                
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Email address</label>
                    <input type="email" id="email" required
                        class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                        placeholder="you@company.com" value="{{ request()->query('email') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">New Password</label>
                    <input type="password" id="password" required minlength="8"
                        class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                        placeholder="••••••••">
                    <p class="text-xs text-indigo-300/50 mt-2">Min 8 chars, uppercase, number, symbol</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" required
                        class="w-full px-4 py-3.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                        placeholder="••••••••">
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all disabled:opacity-50">
                    <span id="btnText">Reset Password</span>
                    <svg id="btnSpinner" class="hidden w-5 h-5 mx-auto animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const errorDiv = document.getElementById('errorDiv');
        const successDiv = document.getElementById('successDiv');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            
            if (password !== confirmation) {
                errorDiv.textContent = 'Passwords do not match';
                errorDiv.classList.remove('hidden');
                return;
            }

            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            try {
                const res = await fetch('/api/auth/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: document.getElementById('token').value,
                        email: document.getElementById('email').value,
                        password: password,
                        password_confirmation: confirmation
                    })
                });

                const result = await res.json();

                if (res.ok) {
                    successDiv.textContent = 'Password reset successful! Redirecting to login...';
                    successDiv.classList.remove('hidden');
                    setTimeout(() => window.location.href = '/login', 2000);
                } else {
                    throw new Error(result.message || 'Reset failed');
                }
            } catch (err) {
                errorDiv.textContent = err.message;
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
