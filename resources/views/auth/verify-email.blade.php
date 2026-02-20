<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .otp-input {
            width: 48px; height: 56px;
            text-align: center; font-size: 1.5rem; font-weight: 700;
            border: 2px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            border-radius: 12px; color: white;
            outline: none; transition: all 0.2s;
        }
        .otp-input:focus { border-color: #818cf8; background: rgba(255,255,255,0.1); }
    </style>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-6">

    <div class="w-full max-w-md text-center">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="text-4xl font-black text-indigo-400">Quizly.</a>

        <!-- Card -->
        <div class="mt-8 bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            <!-- Icon -->
            <div class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Check your email</h1>
            <p class="text-indigo-200/70 mb-8">
                We sent a 6-digit code to your inbox. Enter it below to verify your account.
            </p>

            <!-- Error -->
            <div id="errorDiv" class="hidden mb-4 p-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-sm"></div>
            <!-- Success -->
            <div id="successDiv" class="hidden mb-4 p-3 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-xl text-sm"></div>

            <!-- OTP Input -->
            <form id="otpForm" class="space-y-6">
                <div class="flex justify-center gap-3" id="otpInputs">
                    <input type="text" maxlength="1" class="otp-input" data-index="0" inputmode="numeric" autocomplete="one-time-code">
                    <input type="text" maxlength="1" class="otp-input" data-index="1" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" data-index="2" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" data-index="3" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" data-index="4" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-input" data-index="5" inputmode="numeric">
                </div>

                <button type="submit" id="verifyBtn" class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Verify
                </button>
            </form>

            <!-- Resend -->
            <p class="text-indigo-200/50 text-sm mt-6">
                Didn't get the code?
                <button id="resendBtn" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Resend code</button>
            </p>
            <p id="resendTimer" class="hidden text-indigo-200/40 text-sm mt-2"></p>
        </div>

        <!-- Back Link -->
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white/70 hover:text-white mt-8 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to home
        </a>
    </div>

    <script>
        const token = localStorage.getItem('token');
        if (!token) window.location.href = '/register';

        const inputs = document.querySelectorAll('.otp-input');
        const form = document.getElementById('otpForm');
        const errorDiv = document.getElementById('errorDiv');
        const successDiv = document.getElementById('successDiv');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');

        // Auto-focus and auto-advance OTP inputs
        inputs.forEach((input, i) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
                if (val && i < inputs.length - 1) inputs[i + 1].focus();
                // Auto-submit when all filled
                if (getOtp().length === 6) form.dispatchEvent(new Event('submit'));
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && i > 0) inputs[i - 1].focus();
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
                pasted.split('').forEach((ch, j) => { if (inputs[j]) inputs[j].value = ch; });
                if (pasted.length === 6) form.dispatchEvent(new Event('submit'));
            });
        });

        inputs[0].focus();

        function getOtp() {
            return Array.from(inputs).map(i => i.value).join('');
        }

        function showError(msg) {
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
            successDiv.classList.add('hidden');
        }
        function showSuccess(msg) {
            successDiv.textContent = msg;
            successDiv.classList.remove('hidden');
            errorDiv.classList.add('hidden');
        }
        function clearMessages() {
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
        }

        // Verify OTP
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const otp = getOtp();
            if (otp.length !== 6) { showError('Enter the full 6-digit code.'); return; }

            clearMessages();
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<span class="animate-pulse">Verifying...</span>';

            try {
                const res = await fetch('/api/auth/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                    },
                    body: JSON.stringify({ otp }),
                });

                const data = await res.json();

                if (res.ok) {
                    showSuccess(data.message || 'Email verified!');
                    // Update stored user
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.email_verified = true;
                    localStorage.setItem('user', JSON.stringify(user));
                    // Redirect to plan selection or dashboard
                    setTimeout(() => {
                        window.location.href = user.has_active_subscription ? '/dashboard' : '/select-plan';
                    }, 1500);
                } else {
                    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
                    showError(msg || 'That code didn\'t work. Double-check it or request a new one.');
                    inputs.forEach(i => i.value = '');
                    inputs[0].focus();
                }
            } catch (err) {
                showError('Something went wrong. Give it another try.');
            } finally {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            }
        });

        // Resend OTP
        let resendCooldown = 0;
        resendBtn.addEventListener('click', async () => {
            if (resendCooldown > 0) return;
            resendBtn.disabled = true;
            clearMessages();

            try {
                const res = await fetch('/api/auth/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                    },
                });
                const data = await res.json();
                showSuccess(data.message || 'New code sent! Check your email.');

                // Start cooldown
                resendCooldown = 60;
                const timerEl = document.getElementById('resendTimer');
                timerEl.classList.remove('hidden');
                resendBtn.classList.add('hidden');
                const interval = setInterval(() => {
                    resendCooldown--;
                    timerEl.textContent = `Resend available in ${resendCooldown}s`;
                    if (resendCooldown <= 0) {
                        clearInterval(interval);
                        timerEl.classList.add('hidden');
                        resendBtn.classList.remove('hidden');
                        resendBtn.disabled = false;
                    }
                }, 1000);
            } catch (err) {
                showError('Couldn\'t resend. Try again in a bit.');
                resendBtn.disabled = false;
            }
        });
    </script>

</body>
</html>
