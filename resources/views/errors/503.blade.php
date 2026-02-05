<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 flex items-center justify-center p-6">

    <div class="text-center max-w-md">
        <!-- Icon -->
        <div class="w-24 h-24 bg-amber-500/20 rounded-3xl flex items-center justify-center mx-auto mb-8">
            <svg class="w-12 h-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-bold text-white mb-4">Service Unavailable</h1>
        <p class="text-indigo-200/70 text-lg mb-8">
            We're performing scheduled maintenance. Please check back in a few minutes.
        </p>

        <!-- Actions -->
        <div class="space-y-3">
            <button onclick="window.location.reload()" class="block w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all">
                Refresh Page
            </button>
            <a href="/" class="block w-full py-4 bg-white/10 text-white rounded-xl font-medium hover:bg-white/20 transition-all">
                Go to Homepage
            </a>
        </div>

        <!-- Status -->
        <p class="text-indigo-200/50 text-sm mt-12">
            Check our <a href="#" class="text-indigo-400 hover:text-indigo-300">status page</a> for updates
        </p>
    </div>

</body>
</html>
