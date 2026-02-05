<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Quizly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white">Quizly<span class="text-indigo-400">.</span></h1>
        <p class="text-slate-400 mt-2">Super Admin Access</p>
    </div>

    <!-- Login Card -->
    <div class="bg-slate-800 rounded-2xl shadow-2xl p-8 border border-slate-700">
        <h2 class="text-xl font-semibold text-white mb-6">Administrator Login</h2>

        <form id="loginForm" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email address</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-3 rounded-lg bg-slate-700 border border-slate-600 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="admin@quizly.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg bg-slate-700 border border-slate-600 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="••••••••">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                <span id="btnText">Sign in</span>
                <svg id="spinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <div id="generalError" class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mt-4 hidden"></div>

        <div class="mt-6 text-center">
            <a href="/login" class="text-slate-400 hover:text-white text-sm">← Back to user login</a>
        </div>
    </div>
</div>

<script>
// Sanitize error messages - never show SQL or technical errors
function sanitizeErrorMessage(message) {
    if (!message || typeof message !== 'string') {
        return 'An unexpected error occurred. Please try again.';
    }
    const technicalPatterns = [
        'SQLSTATE', 'SQL:', 'Query', 'Connection refused', 
        'Base table', 'doesn\'t exist', 'PDO', 'mysql',
        'ECONNREFUSED', 'timeout', 'Exception'
    ];
    for (const pattern of technicalPatterns) {
        if (message.toLowerCase().includes(pattern.toLowerCase())) {
            return 'Service temporarily unavailable. Please try again later.';
        }
    }
    return message;
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    const errorDiv = document.getElementById('generalError');
    
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Signing in...';
    errorDiv.classList.add('hidden');
    
    try {
        const response = await fetch('/api/admin/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });
        
        const data = await response.json();
        
        if (response.ok) {
            localStorage.setItem('adminToken', data.token);
            localStorage.setItem('admin', JSON.stringify(data.admin));
            window.location.href = '/admin/dashboard';
        } else {
            errorDiv.textContent = sanitizeErrorMessage(data.message || data.errors?.email?.[0] || 'Login failed');
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = sanitizeErrorMessage(err.message || 'Network error. Please try again.');
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Sign in';
    }
});
</script>

</body>
</html>
