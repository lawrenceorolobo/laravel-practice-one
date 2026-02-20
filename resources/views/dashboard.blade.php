<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Quizly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
                    }
                }
            }
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism */
        .glass { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-dark { 
            background: rgba(30, 41, 59, 0.8); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #cbd5e1 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 8px;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Smooth hover effects */
        .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 40px -12px rgba(0,0,0,0.15); }
        
        /* Sidebar active */
        .sidebar-link.active { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; }
        .sidebar-link.active svg { color: white; }
        
        /* Progress ring */
        .progress-ring { transform: rotate(-90deg); }
    </style>
</head>
<body class="bg-slate-100 antialiased text-slate-900">

<!-- Mobile Navigation -->
<div class="md:hidden glass fixed top-0 left-0 right-0 z-50 py-4 px-6 flex justify-between items-center">
    <h1 class="text-xl font-bold text-indigo-600">Quizly.</h1>
    <button id="mobileMenuBtn" class="p-2 rounded-lg hover:bg-slate-100">
        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

<!-- Mobile Menu Overlay -->
<div id="mobileOverlay" class="hidden fixed inset-0 bg-black/50 z-[60] md:hidden" onclick="closeMobileMenu()"></div>

<!-- Mobile Menu Drawer -->
<div id="mobileDrawer" class="fixed top-0 left-0 h-full w-72 bg-white z-[70] transform -translate-x-full transition-transform duration-300 md:hidden">
    <div class="p-6 border-b flex justify-between items-center">
        <h1 class="text-xl font-bold text-indigo-600">Quizly.</h1>
        <button onclick="closeMobileMenu()" class="p-2 rounded-lg hover:bg-slate-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <nav class="p-4 space-y-1">
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 bg-indigo-600 text-white rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="/assessments" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-100 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Assessments
        </a>
        <a href="/candidates" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-100 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Candidates
        </a>
        <a href="/analytics" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-100 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Analytics
        </a>
        <a href="/settings" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-100 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
        </a>
    </nav>
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t">
        <button onclick="logout()" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sign out
        </button>
    </div>
</div>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-72 h-screen sticky top-0 glass border-r border-slate-200/50">
        <div class="p-8">
            <a href="/" class="text-2xl font-bold text-indigo-600">Quizly.</a>
            <p class="text-xs text-slate-500 mt-1">Assessment Platform</p>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <a href="/dashboard" class="sidebar-link active flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="/assessments" id="navAssessments" class="sidebar-link flex items-center gap-3 px-4 py-3.5 text-slate-600 hover:bg-slate-100 rounded-xl font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Assessments
                <span class="ml-auto bg-indigo-100 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full" id="assessmentsBadge">0</span>
            </a>

            <a href="/candidates" id="navCandidates" class="sidebar-link flex items-center gap-3 px-4 py-3.5 text-slate-600 hover:bg-slate-100 rounded-xl font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Candidates
            </a>

            <a href="/analytics" id="navAnalytics" class="sidebar-link flex items-center gap-3 px-4 py-3.5 text-slate-600 hover:bg-slate-100 rounded-xl font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>

            <a href="/settings" id="navSettings" class="sidebar-link flex items-center gap-3 px-4 py-3.5 text-slate-600 hover:bg-slate-100 rounded-xl font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t border-slate-200/50">
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-100 transition cursor-pointer" id="userProfile">
                <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold text-lg" id="userAvatar">U</div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 truncate" id="userName">Loading...</p>
                    <p class="text-sm text-slate-500 truncate" id="userEmail">...</p>
                </div>
            </div>
            <button onclick="logout()" class="mt-3 w-full flex items-center justify-center gap-2 px-4 py-2.5 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign out
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 pt-20 md:p-10 md:pt-8">
        <!-- Top Bar -->
        <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900">Dashboard</h2>
                <p class="text-slate-500 mt-1">Welcome back! Here's your assessment overview.</p>
            </div>

            <button onclick="createAssessment()" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition flex items-center gap-2 shadow-lg shadow-indigo-500/25 hover-lift">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Assessment
            </button>
        </div>

        <!-- Stats Grid with Skeleton -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Assessments -->
            <div class="glass rounded-2xl p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <p class="text-slate-500 font-medium">Total Assessments</p>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div id="statAssessmentsContainer">
                    <div class="skeleton h-10 w-16 mt-3"></div>
                    <div class="skeleton h-4 w-24 mt-2"></div>
                </div>
                <div id="statAssessmentsData" class="hidden">
                    <h3 class="text-4xl font-bold mt-3 text-slate-900" id="statAssessments">0</h3>
                    <p class="text-sm mt-2" id="statAssessmentsDelta"></p>
                </div>
            </div>

            <!-- Total Candidates -->
            <div class="glass rounded-2xl p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <p class="text-slate-500 font-medium">Total Candidates</p>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <div id="statCandidatesContainer">
                    <div class="skeleton h-10 w-16 mt-3"></div>
                    <div class="skeleton h-4 w-24 mt-2"></div>
                </div>
                <div id="statCandidatesData" class="hidden">
                    <h3 class="text-4xl font-bold mt-3 text-slate-900" id="statCandidates">0</h3>
                    <p class="text-sm mt-2" id="statCandidatesDelta"></p>
                </div>
            </div>

            <!-- Completion Rate -->
            <div class="glass rounded-2xl p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <p class="text-slate-500 font-medium">Completion Rate</p>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div id="statCompletionContainer">
                    <div class="skeleton h-10 w-16 mt-3"></div>
                    <div class="skeleton h-4 w-24 mt-2"></div>
                </div>
                <div id="statCompletionData" class="hidden">
                    <h3 class="text-4xl font-bold mt-3 text-slate-900" id="statCompletion">0%</h3>
                    <p class="text-sm mt-2" id="statCompletionDelta"></p>
                </div>
            </div>

            <!-- Avg Score -->
            <div class="glass rounded-2xl p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <p class="text-slate-500 font-medium">Avg. Score</p>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
                <div id="statAvgScoreContainer">
                    <div class="skeleton h-10 w-16 mt-3"></div>
                    <div class="skeleton h-4 w-24 mt-2"></div>
                </div>
                <div id="statAvgScoreData" class="hidden">
                    <h3 class="text-4xl font-bold mt-3 text-slate-900" id="statAvgScore">0%</h3>
                    <p class="text-sm text-slate-500 mt-2">Across all tests</p>
                </div>
            </div>
        </div>

        <!-- Recent Assessments + Activity -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Recent Assessments -->
            <div class="glass lg:col-span-2 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-200/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Recent Assessments</h3>
                    <a href="/assessments" class="text-indigo-600 text-sm font-semibold hover:text-indigo-700 flex items-center gap-1">
                        View all 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-slate-500 border-b border-slate-200/50">
                                <th class="px-6 py-4 font-semibold">Title</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Candidates</th>
                                <th class="px-6 py-4 font-semibold">Avg. Score</th>
                            </tr>
                        </thead>
                        <tbody id="assessmentsTable">
                            <!-- Skeleton rows -->
                            <tr id="skeletonRow1">
                                <td class="px-6 py-4"><div class="skeleton h-5 w-40"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-6 w-20 rounded-full"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-8"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-12"></div></td>
                            </tr>
                            <tr id="skeletonRow2">
                                <td class="px-6 py-4"><div class="skeleton h-5 w-32"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-6 w-16 rounded-full"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-8"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-12"></div></td>
                            </tr>
                            <tr id="skeletonRow3">
                                <td class="px-6 py-4"><div class="skeleton h-5 w-36"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-6 w-24 rounded-full"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-8"></div></td>
                                <td class="px-6 py-4"><div class="skeleton h-5 w-12"></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="glass rounded-2xl">
                <div class="p-6 border-b border-slate-200/50">
                    <h3 class="text-lg font-bold text-slate-900">Recent Activity</h3>
                </div>

                <div class="p-6" id="activityContainer">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="skeleton w-10 h-10 rounded-full flex-shrink-0"></div>
                            <div class="flex-1">
                                <div class="skeleton h-4 w-full mb-2"></div>
                                <div class="skeleton h-3 w-1/2"></div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="skeleton w-10 h-10 rounded-full flex-shrink-0"></div>
                            <div class="flex-1">
                                <div class="skeleton h-4 w-full mb-2"></div>
                                <div class="skeleton h-3 w-1/3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 hidden" id="activityData">
                    <ul class="space-y-4" id="activityList"></ul>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-10">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="createAssessment()" class="glass p-6 rounded-2xl hover:shadow-lg transition group text-left hover-lift">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 transition">
                        <svg class="w-7 h-7 text-indigo-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900">Create Assessment</h4>
                    <p class="text-sm text-slate-500 mt-1">Build a new test</p>
                </button>

                <button onclick="inviteCandidates()" class="glass p-6 rounded-2xl hover:shadow-lg transition group text-left hover-lift">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 transition">
                        <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900">Invite Candidates</h4>
                    <p class="text-sm text-slate-500 mt-1">Add test takers</p>
                </button>

                <button onclick="viewAnalytics()" class="glass p-6 rounded-2xl hover:shadow-lg transition group text-left hover-lift">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-600 transition">
                        <svg class="w-7 h-7 text-purple-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900">View Analytics</h4>
                    <p class="text-sm text-slate-500 mt-1">Check performance</p>
                </button>

                <button onclick="exportResults()" class="glass p-6 rounded-2xl hover:shadow-lg transition group text-left hover-lift">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-600 transition">
                        <svg class="w-7 h-7 text-amber-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-900">Export Results</h4>
                    <p class="text-sm text-slate-500 mt-1">Download reports</p>
                </button>
            </div>
        </div>
    </main>
</div>

<script>
// Auth check
const token = localStorage.getItem('token');
const user = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) {
    window.location.href = '/login';
} else {
    document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
    document.getElementById('userEmail').textContent = user.email;
    document.getElementById('userAvatar').textContent = user.first_name?.charAt(0).toUpperCase() || 'U';
}

async function logout() {
    try {
        await fetch('/api/auth/logout', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
    } catch (err) {}
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login';
}

// Fetch dashboard stats from backend
async function loadDashboardStats() {
    try {
        const res = await fetch('/api/dashboard/stats', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        
        if (res.ok) {
            const data = await res.json();
            
            // Update stats with real data
            showStat('Assessments', data.total_assessments || 0, data.assessments_this_week || 0);
            showStat('Candidates', data.total_candidates || 0, data.candidates_this_week || 0);
            showStat('Completion', data.completion_rate || 0, data.completion_change || 0, true);
            showStat('AvgScore', data.avg_score || 0, null);
            
            document.getElementById('assessmentsBadge').textContent = data.total_assessments || 0;
        } else {
            // Fallback to calculating from assessments
            await loadDashboardFallback();
        }
    } catch (err) {
        console.log('Stats API not available, using fallback');
        await loadDashboardFallback();
    }
}

async function loadDashboardFallback() {
    try {
        const res = await fetch('/api/assessments', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        
        if (res.ok) {
            const data = await res.json();
            const assessments = data.data || [];
            
            // Calculate stats from assessments
            const totalAssessments = assessments.length;
            let totalCandidates = 0;
            let totalCompleted = 0;
            let totalScore = 0;
            let scoreCount = 0;
            
            // Get assessments from this week
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            let assessmentsThisWeek = 0;
            let candidatesThisWeek = 0;
            
            assessments.forEach(a => {
                const invitees = a.invitees_count || 0;
                const completed = a.completed_count || 0;
                totalCandidates += invitees;
                totalCompleted += completed;
                
                if (a.avg_score) {
                    totalScore += parseFloat(a.avg_score);
                    scoreCount++;
                }
                
                if (new Date(a.created_at) >= weekAgo) {
                    assessmentsThisWeek++;
                    candidatesThisWeek += invitees;
                }
            });
            
            const completionRate = totalCandidates > 0 ? Math.round((totalCompleted / totalCandidates) * 100) : 0;
            const avgScore = scoreCount > 0 ? Math.round(totalScore / scoreCount) : 0;
            
            showStat('Assessments', totalAssessments, assessmentsThisWeek);
            showStat('Candidates', totalCandidates, candidatesThisWeek);
            showStat('Completion', completionRate, 0, true);
            showStat('AvgScore', avgScore, null);
            
            document.getElementById('assessmentsBadge').textContent = totalAssessments;
        } else {
            showEmptyStats();
        }
    } catch (err) {
        console.log('Could not load dashboard data');
        showEmptyStats();
    }
}

function showStat(name, value, delta, isPercent = false) {
    document.getElementById(`stat${name}Container`).classList.add('hidden');
    document.getElementById(`stat${name}Data`).classList.remove('hidden');
    document.getElementById(`stat${name}`).textContent = isPercent ? `${value}%` : value;
    
    const deltaEl = document.getElementById(`stat${name}Delta`);
    if (deltaEl && delta !== null) {
        if (delta > 0) {
            deltaEl.textContent = `+${delta} this week`;
            deltaEl.className = 'text-sm mt-2 text-emerald-600';
        } else if (delta < 0) {
            deltaEl.textContent = `${delta} this week`;
            deltaEl.className = 'text-sm mt-2 text-red-600';
        } else {
            deltaEl.textContent = 'No change this week';
            deltaEl.className = 'text-sm mt-2 text-slate-500';
        }
    }
}

function showEmptyStats() {
    showStat('Assessments', 0, 0);
    showStat('Candidates', 0, 0);
    showStat('Completion', 0, 0, true);
    showStat('AvgScore', 0, null);
}

// Load assessments table
async function loadAssessments() {
    try {
        const res = await fetch('/api/assessments', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        
        const tbody = document.getElementById('assessmentsTable');
        
        // Remove skeleton rows
        document.getElementById('skeletonRow1')?.remove();
        document.getElementById('skeletonRow2')?.remove();
        document.getElementById('skeletonRow3')?.remove();
        
        if (res.ok) {
            const data = await res.json();
            const assessments = data.data || [];
            
            if (assessments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 mb-4">No assessments yet</p>
                            <button onclick="createAssessment()" class="text-indigo-600 font-semibold hover:text-indigo-700">Create your first one →</button>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = assessments.slice(0, 5).map(a => {
                    const statusColors = {
                        'draft': 'bg-slate-100 text-slate-600',
                        'published': 'bg-emerald-100 text-emerald-700',
                        'closed': 'bg-red-100 text-red-700'
                    };
                    const statusClass = statusColors[a.status] || statusColors.draft;
                    
                    return `
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer" onclick="viewAssessment('${a.id}')">
                            <td class="px-6 py-4 font-medium text-slate-900">${a.title}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                    ${a.status || 'Draft'}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">${a.invitees_count || 0}</td>
                            <td class="px-6 py-4 font-medium">${a.avg_score ? parseFloat(a.avg_score).toFixed(1) + '%' : '-'}</td>
                        </tr>
                    `;
                }).join('');
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500">Failed to load assessments</td></tr>';
        }
    } catch (err) {
        console.log('Could not load assessments');
    }
}

// Load activity
async function loadActivity() {
    try {
        const res = await fetch('/api/activity', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        
        document.getElementById('activityContainer').classList.add('hidden');
        document.getElementById('activityData').classList.remove('hidden');
        
        const list = document.getElementById('activityList');
        
        if (res.ok) {
            const data = await res.json();
            const activities = data.data || [];
            
            if (activities.length === 0) {
                list.innerHTML = `
                    <li class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-slate-600 text-sm">Welcome to Quizly! Start by creating your first assessment.</p>
                            <p class="text-xs text-slate-400 mt-1">Just now</p>
                        </div>
                    </li>
                `;
            } else {
                list.innerHTML = activities.slice(0, 5).map(a => `
                    <li class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-${a.color || 'indigo'}-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-${a.color || 'indigo'}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${a.icon || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-slate-600 text-sm">${a.message}</p>
                            <p class="text-xs text-slate-400 mt-1">${a.time_ago || 'Just now'}</p>
                        </div>
                    </li>
                `).join('');
            }
        } else {
            showDefaultActivity(list);
        }
    } catch (err) {
        const list = document.getElementById('activityList');
        showDefaultActivity(list);
    }
}

function showDefaultActivity(list) {
    list.innerHTML = `
        <li class="flex items-start gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-slate-600 text-sm">Welcome to Quizly! Start by creating your first assessment.</p>
                <p class="text-xs text-slate-400 mt-1">Just now</p>
            </div>
        </li>
    `;
}

// Quick Action Functions
function createAssessment() {
    window.location.href = '/assessments/create';
}

function inviteCandidates() {
    window.location.href = '/candidates';
}

function viewAnalytics() {
    window.location.href = '/analytics';
}

function exportResults() {
    window.location.href = '/analytics';
}

function viewAssessment(id) {
    window.location.href = `/assessments/${id}`;
}

// Initialize
loadDashboardStats();
loadAssessments();
loadActivity();

// Mobile menu
function openMobileMenu() {
    document.getElementById('mobileOverlay').classList.remove('hidden');
    document.getElementById('mobileDrawer').classList.remove('-translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closeMobileMenu() {
    document.getElementById('mobileOverlay').classList.add('hidden');
    document.getElementById('mobileDrawer').classList.add('-translate-x-full');
    document.body.style.overflow = '';
}
document.getElementById('mobileMenuBtn')?.addEventListener('click', openMobileMenu);
</script>

</body>
</html>