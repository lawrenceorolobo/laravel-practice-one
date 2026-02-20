<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Quizly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .success-check { animation: scaleIn 0.5s ease-out; }
        @keyframes scaleIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <h1 class="text-4xl font-black text-white mb-8">Quizly.</h1>

        <!-- Success State -->
        <div id="successState" class="bg-white/95 rounded-3xl p-8 shadow-2xl hidden">
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 success-check">
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">You're all set!</h2>
            <p class="text-slate-500 mb-6">Your subscription is now active.</p>
            <p class="text-slate-400 text-sm">Taking you to your dashboard...</p>
        </div>

        <!-- Error State -->
        <div id="errorState" class="bg-white/95 rounded-3xl p-8 shadow-2xl hidden">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2" id="errorTitle">Payment didn't go through</h2>
            <p id="errorMessage" class="text-slate-500 mb-6">Something went wrong. Give it another try.</p>
            <a href="/select-plan" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
                Try Again
            </a>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const status = params.get('status');

        // Flutterwave redirects with ?status=successful|completed|cancelled|failed
        // The webhook already processed the payment server-side — no polling needed
        if (['successful', 'completed'].includes(status)) {
            showSuccess();
        } else if (status === 'cancelled') {
            showError('Payment cancelled', "No worries — you can try again when you're ready.");
        } else {
            showError("Payment didn't go through", 'The payment was declined. Please try again.');
        }

        function showSuccess() {
            localStorage.removeItem('pending_tx_ref');
            document.getElementById('successState').classList.remove('hidden');
            // Refresh user data in localStorage so dashboard doesn't redirect to select-plan
            const token = localStorage.getItem('token');
            if (token) {
                fetch('/api/auth/profile', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(data => { if (data) localStorage.setItem('user', JSON.stringify(data.user || data)); })
                    .finally(() => { setTimeout(() => { window.location.href = '/dashboard'; }, 1500); });
            } else {
                setTimeout(() => { window.location.href = '/dashboard'; }, 2500);
            }
        }

        function showError(title, message) {
            document.getElementById('errorTitle').textContent = title;
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorState').classList.remove('hidden');
        }
    </script>
</body>
</html>
