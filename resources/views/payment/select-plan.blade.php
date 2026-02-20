<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Plan - Quizly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #4f46e5; min-height: 100vh; }
        .card { backdrop-filter: blur(10px); }
        .loading { pointer-events: none; opacity: 0.7; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white">Quizly.</h1>
            <p class="text-white/80 mt-2">Complete your subscription to continue</p>
        </div>

        <!-- Plan Card -->
        <div id="planContainer" class="bg-white/95 card rounded-3xl p-8 shadow-2xl">
            <!-- Loading skeleton -->
            <div id="loadingSkeleton" class="space-y-4 animate-pulse">
                <div class="h-8 bg-slate-200 rounded w-1/2"></div>
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-12 bg-slate-200 rounded w-full mt-6"></div>
                <div class="space-y-2 mt-6">
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
                <div class="h-14 bg-slate-200 rounded-xl mt-8"></div>
            </div>

            <!-- Plan content (hidden until loaded) -->
            <div id="planContent" class="hidden">
                <div class="text-center mb-6">
                    <span class="inline-block bg-indigo-100 text-indigo-600 text-sm font-semibold px-3 py-1 rounded-full mb-4">Premium Subscription</span>
                    <h2 id="planName" class="text-2xl font-bold text-slate-900"></h2>
                </div>

                <div class="text-center mb-8">
                    <span id="planPrice" class="text-5xl font-black text-slate-900"></span>
                    <span class="text-slate-500">/month</span>
                </div>

                <ul id="planFeatures" class="space-y-3 mb-8">
                    <!-- Features will be inserted here -->
                </ul>

                <button id="payButton" onclick="initializePayment()" 
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Continue to Payment
                </button>

                <p class="text-center text-slate-400 text-sm mt-4">
                    Secure payment powered by Flutterwave
                </p>
            </div>

            <!-- Error state -->
            <div id="errorState" class="hidden text-center py-8">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Something went wrong</h3>
                <p id="errorMessage" class="text-slate-500 mb-6"></p>
                <button onclick="location.reload()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition">
                    Try Again
                </button>
            </div>
        </div>

        <!-- Already subscribed notice -->
        <p class="text-center text-white/60 text-sm mt-6">
            Already subscribed? <a href="/dashboard" class="text-white underline">Go to Dashboard</a>
        </p>
    </div>

    <script>
        const token = localStorage.getItem('token');
        let selectedPlan = null;

        // Check if user is logged in
        if (!token) {
            window.location.href = '/login?redirect=/select-plan';
        }

        // Load plans
        async function loadPlans() {
            try {
                const res = await fetch('/api/subscription/plans');
                const data = await res.json();
                const plans = data.plans || [];

                if (!plans.length) {
                    showError('No plans available right now. Reach out to support.');
                    return;
                }

                // Get first active plan (there's only one)
                selectedPlan = plans[0];
                displayPlan(selectedPlan);
            } catch (e) {
                showError('Couldn\'t load plans. Give it another try.');
            }
        }

        function displayPlan(plan) {
            document.getElementById('planName').textContent = plan.name;
            document.getElementById('planPrice').textContent = '₦' + (plan.monthly_price / 1000).toFixed(0) + 'K';
            
            const features = typeof plan.features === 'string' ? JSON.parse(plan.features) : (plan.features || []);
            const featuresHtml = features.map(f => `
                <li class="flex items-center gap-3 text-slate-600">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    ${f}
                </li>
            `).join('');
            document.getElementById('planFeatures').innerHTML = featuresHtml;

            document.getElementById('loadingSkeleton').classList.add('hidden');
            document.getElementById('planContent').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('loadingSkeleton').classList.add('hidden');
            document.getElementById('planContent').classList.add('hidden');
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorState').classList.remove('hidden');
        }

        async function initializePayment() {
            if (!selectedPlan) return;

            const btn = document.getElementById('payButton');
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Initializing...</span>';

            try {
                const res = await fetch('/api/payments/initialize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                    },
                    body: JSON.stringify({ plan_id: selectedPlan.id }),
                });

                const data = await res.json();

                if (res.ok && data.payment_link) {
                    // Store tx_ref for verification
                    localStorage.setItem('pending_tx_ref', data.tx_ref);
                    // Redirect to Flutterwave
                    window.location.href = data.payment_link;
                } else {
                    throw new Error(data.message || 'Couldn\'t start payment. Try again.');
                }
            } catch (e) {
                btn.disabled = false;
                btn.textContent = 'Continue to Payment';
                showError(e.message);
            }
        }

        // Clean error params from URL and load plans
        if (window.location.search) {
            history.replaceState(null, '', window.location.pathname);
        }
        loadPlans();
    </script>
</body>
</html>
