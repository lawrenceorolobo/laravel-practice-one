<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Assessment | Quizly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

<!-- Loading State -->
<div id="loadingState" class="text-center">
    <svg class="w-12 h-12 text-indigo-600 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <p class="text-slate-600">Loading assessment...</p>
</div>

<!-- Join Form -->
<div id="joinState" class="hidden bg-white rounded-2xl shadow-xl max-w-lg w-full p-8">
    <div class="text-center mb-6">
        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900" id="assessmentTitle">Assessment</h1>
        <p class="text-slate-500 mt-2" id="assessmentDesc">Enter your details to begin.</p>
    </div>

    <div id="assessmentInfo" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6 hidden">
        <div class="flex justify-between text-sm mb-1">
            <span class="text-slate-600">Duration:</span>
            <span class="font-medium text-slate-900" id="duration">-</span>
        </div>
    </div>

    <form id="joinForm" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">First name</label>
                <input type="text" id="firstName" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Last name</label>
                <input type="text" id="lastName" required
                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email address</label>
            <input type="email" id="email" required
                class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="you@example.com">
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
            <strong>Important:</strong> Use a valid email address you have access to. This test will monitor tab switching and fullscreen exits during the assessment.
        </div>

        <button type="submit" id="submitBtn"
            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
            Join Assessment
        </button>
    </form>

    <div id="joinError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mt-4 hidden"></div>
</div>

<!-- Error State -->
<div id="errorState" class="hidden bg-white rounded-2xl shadow-xl max-w-lg w-full p-8 text-center">
    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </div>
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Unable to Access Assessment</h2>
    <p class="text-slate-600" id="errorMessage">This assessment is not available.</p>
</div>

<script>
const ACCESS_CODE = '{{ $accessCode }}';

document.addEventListener('DOMContentLoaded', () => {
    if (!ACCESS_CODE) { showError('Invalid assessment link.'); return; }
    validateAccess();
});

async function validateAccess() {
    try {
        const res = await fetch(`/api/test/access/${ACCESS_CODE}`);
        const data = await res.json();

        if (!res.ok || !data.valid) {
            showError(data.message || 'Assessment not available.');
            return;
        }

        showJoinForm(data.assessment);
    } catch (err) {
        showError('Network error. Please refresh.');
    }
}

function showJoinForm(assessment) {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('joinState').classList.remove('hidden');
    document.getElementById('assessmentTitle').textContent = assessment.title;
    if (assessment.description) {
        document.getElementById('assessmentDesc').textContent = assessment.description;
    }
    if (assessment.duration_minutes) {
        document.getElementById('assessmentInfo').classList.remove('hidden');
        document.getElementById('duration').textContent = assessment.duration_minutes + ' minutes';
    }
}

function showError(message) {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('joinState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;
}

document.getElementById('joinForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorDiv = document.getElementById('joinError');
    const submitBtn = document.getElementById('submitBtn');
    errorDiv.classList.add('hidden');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Joining...';

    try {
        const res = await fetch(`/api/test/join/${ACCESS_CODE}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                first_name: document.getElementById('firstName').value.trim(),
                last_name: document.getElementById('lastName').value.trim(),
                email: document.getElementById('email').value.trim(),
            }),
        });

        const data = await res.json();

        if (data.redirect_token) {
            // Redirect to the test page with the token
            window.location.href = `/test/${data.redirect_token}`;
            return;
        }

        if (!res.ok) {
            errorDiv.textContent = data.message || Object.values(data.errors || {}).flat()[0] || 'Unable to join.';
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Join Assessment';
    }
});
</script>

</body>
</html>
