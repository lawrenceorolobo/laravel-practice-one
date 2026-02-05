<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quizly')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); }
        .skeleton { background: linear-gradient(90deg, #e2e8f0 25%, #cbd5e1 50%, #e2e8f0 75%); background-size: 200% 100%; animation: skel 1.5s infinite; border-radius: 8px; }
        @keyframes skel { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 40px -12px rgba(0,0,0,0.15); }
        .sidebar-link.active { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; }
        
        /* Dark Mode */
        body.dark { background: #0f172a; color: #f1f5f9; }
        body.dark .glass { background: rgba(30,41,59,0.8); border-color: rgba(71,85,105,0.5); }
        body.dark .text-slate-900 { color: #f1f5f9; }
        body.dark .text-slate-600 { color: #94a3b8; }
        body.dark .text-slate-500 { color: #64748b; }
        body.dark .bg-white { background: #1e293b; }
        body.dark .border-slate-200 { border-color: #334155; }
        body.dark .hover\:bg-slate-100:hover { background: #334155; }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-100 antialiased text-slate-900">

<!-- Mobile Header -->
<div class="md:hidden glass fixed top-0 left-0 right-0 z-50 py-4 px-6 flex justify-between items-center">
    <h1 class="text-xl font-bold text-indigo-600">Quizly.</h1>
    <div class="flex items-center gap-2">
        <button id="mobileUserThemeBtn" class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700" title="Toggle theme">
            <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg class="w-5 h-5 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        </button>
        <button id="mobileMenuBtn" class="p-2 rounded-lg hover:bg-slate-100">
            <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</div>

<!-- Mobile Overlay -->
<div id="mobileOverlay" class="hidden fixed inset-0 bg-black/50 z-[60] md:hidden" onclick="closeMobileMenu()"></div>

<!-- Mobile Drawer -->
<div id="mobileDrawer" class="fixed top-0 left-0 h-full w-72 bg-white z-[70] transform -translate-x-full transition-transform duration-300 md:hidden">
    <div class="p-6 border-b flex justify-between items-center">
        <h1 class="text-xl font-bold text-indigo-600">Quizly.</h1>
        <button onclick="closeMobileMenu()" class="p-2 rounded-lg hover:bg-slate-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <nav class="p-4 space-y-1">
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 {{ request()->is('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="/assessments" class="flex items-center gap-3 px-4 py-3 {{ request()->is('assessments*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Assessments
        </a>
        <a href="/candidates" class="flex items-center gap-3 px-4 py-3 {{ request()->is('candidates*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Candidates
        </a>
        <a href="/analytics" class="flex items-center gap-3 px-4 py-3 {{ request()->is('analytics*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Analytics
        </a>
        <a href="/settings" class="flex items-center gap-3 px-4 py-3 {{ request()->is('settings*') ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl font-medium">
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

<div class="flex min-h-screen pt-16 md:pt-0">
    <!-- Sidebar -->
    <aside class="hidden md:flex flex-col w-72 h-screen sticky top-0 glass border-r border-slate-200/50">
        <div class="p-8">
            <a href="/" class="text-2xl font-bold text-indigo-600">Quizly.</a>
            <p class="text-xs text-slate-500 mt-1">Assessment Platform</p>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all {{ !request()->is('dashboard') ? 'text-slate-600 hover:bg-slate-100' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="/assessments" class="sidebar-link {{ request()->is('assessments*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all {{ !request()->is('assessments*') ? 'text-slate-600 hover:bg-slate-100' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Assessments
            </a>
            <a href="/candidates" class="sidebar-link {{ request()->is('candidates*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all {{ !request()->is('candidates*') ? 'text-slate-600 hover:bg-slate-100' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Candidates
            </a>
            <a href="/analytics" class="sidebar-link {{ request()->is('analytics*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all {{ !request()->is('analytics*') ? 'text-slate-600 hover:bg-slate-100' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Analytics
            </a>
            <a href="/settings" class="sidebar-link {{ request()->is('settings*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all {{ !request()->is('settings*') ? 'text-slate-600 hover:bg-slate-100' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>

        <div class="p-4 border-t border-slate-200/50">
            <div class="flex items-center gap-3 p-3 rounded-xl">
                <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold" id="userAvatar">U</div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 truncate" id="userName">Loading...</p>
                    <p class="text-sm text-slate-500 truncate" id="userEmail">...</p>
                </div>
            </div>
            <button id="userThemeBtn" class="mt-2 w-full flex items-center justify-center gap-2 px-4 py-2.5 text-slate-600 hover:bg-slate-100 rounded-xl transition font-medium" title="Toggle theme">
                <svg class="w-4 h-4 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="w-4 h-4 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <span class="theme-text">Dark Mode</span>
            </button>
            <button onclick="logout()" class="mt-1 w-full flex items-center justify-center gap-2 px-4 py-2.5 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign out
            </button>
        </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-6 md:p-10">
        @yield('content')
    </main>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-20 md:top-6 right-6 z-[100] flex flex-col gap-3"></div>

<script>
const token = localStorage.getItem('token');
const user = JSON.parse(localStorage.getItem('user') || 'null');
if (!token || !user) { window.location.href = '/login'; }
else {
    document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
    document.getElementById('userEmail').textContent = user.email;
    document.getElementById('userAvatar').textContent = user.first_name?.charAt(0).toUpperCase() || 'U';
}
async function logout() {
    try { await fetch('/api/auth/logout', { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } }); } catch(e){}
    localStorage.removeItem('token'); localStorage.removeItem('user'); window.location.href = '/login';
}
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

// Toast Notification System
function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const icons = {
        success: `<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
        error: `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
        info: `<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
    };
    
    const bgColors = {
        success: 'border-emerald-200 bg-gradient-to-r from-emerald-50 to-white',
        error: 'border-red-200 bg-gradient-to-r from-red-50 to-white',
        warning: 'border-amber-200 bg-gradient-to-r from-amber-50 to-white',
        info: 'border-indigo-200 bg-gradient-to-r from-indigo-50 to-white'
    };
    
    toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border backdrop-blur-sm ${bgColors[type]} transform translate-x-full opacity-0 transition-all duration-300 max-w-sm`;
    toast.innerHTML = `
        <div class="flex-shrink-0">${icons[type]}</div>
        <p class="flex-1 text-sm font-medium text-slate-700">${message}</p>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    });
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Convenience functions
function toastSuccess(message) { showToast(message, 'success'); }
function toastError(message) { showToast(message, 'error'); }
function toastWarning(message) { showToast(message, 'warning'); }
function toastInfo(message) { showToast(message, 'info'); }

// Confirmation Modal System
let confirmResolve = null;
function showConfirm(title, message, confirmText = 'Confirm', type = 'danger') {
    return new Promise((resolve) => {
        confirmResolve = resolve;
        const modal = document.getElementById('confirmModal');
        const modalTitle = document.getElementById('confirmTitle');
        const modalMessage = document.getElementById('confirmMessage');
        const confirmBtn = document.getElementById('confirmBtn');
        
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        confirmBtn.textContent = confirmText;
        
        // Set button color based on type
        confirmBtn.className = `px-5 py-2.5 rounded-lg font-semibold transition-all ${
            type === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' :
            type === 'warning' ? 'bg-amber-500 hover:bg-amber-600 text-white' :
            'bg-indigo-600 hover:bg-indigo-700 text-white'
        }`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
}

function closeConfirmModal(result) {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (confirmResolve) {
        confirmResolve(result);
        confirmResolve = null;
    }
}
</script>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-[110] items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target === this) closeConfirmModal(false)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-100 transition-all">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="confirmTitle" class="text-lg font-bold text-slate-900">Confirm Action</h3>
                    <p id="confirmMessage" class="text-sm text-slate-600">Are you sure you want to proceed?</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closeConfirmModal(false)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold border-2 border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button id="confirmBtn" onclick="closeConfirmModal(true)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold bg-red-600 text-white hover:bg-red-700 transition-all">
                Confirm
            </button>
        </div>
    </div>
</div>

@yield('scripts')

<script>
// Theme toggle 
function updateUserThemeIcons() {
    const isDark = document.body.classList.contains('dark');
    document.querySelectorAll('.sun-icon').forEach(el => el.classList.toggle('hidden', isDark));
    document.querySelectorAll('.moon-icon').forEach(el => el.classList.toggle('hidden', !isDark));
    document.querySelectorAll('.theme-text').forEach(el => el.textContent = isDark ? 'Light Mode' : 'Dark Mode');
}

function toggleUserTheme() {
    document.body.classList.toggle('dark');
    const isDark = document.body.classList.contains('dark');
    localStorage.setItem('userTheme', isDark ? 'dark' : 'light');
    updateUserThemeIcons();
}

if (localStorage.getItem('userTheme') === 'dark') {
    document.body.classList.add('dark');
}
updateUserThemeIcons();

document.getElementById('userThemeBtn')?.addEventListener('click', toggleUserTheme);
document.getElementById('mobileUserThemeBtn')?.addEventListener('click', toggleUserTheme);
</script>
</body>
</html>


